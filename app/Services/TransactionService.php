<?php

namespace App\Services;

use App\DTO\RefundTransactionResult;
use App\DTO\StoreTransactionResult;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\PurchaseRepositoryInterface;
use App\Interfaces\TransactionRepositoryInterface;
use App\Models\Transaction;
use App\Services\Gateways\BearerTokenGatewayService;
use App\Services\Gateways\GatewayConfigurationService;
use App\Services\Gateways\HeaderAuthGatewayService;
use Illuminate\Database\QueryException;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    public function __construct(
        protected TransactionRepositoryInterface $transactionRepository,
        protected ProductRepositoryInterface     $productRepository,
        protected PurchaseRepositoryInterface    $purchaseRepository,
        protected BearerTokenGatewayService      $bearerTokenGatewayService,
        protected HeaderAuthGatewayService       $headerAuthGatewayService,
        protected GatewayConfigurationService    $gatewayConfigurationService
    ) {}

    public function store(array $requestData): StoreTransactionResult
    {
        $existing = $this->transactionRepository->findByClientAndPaymentKey(
            (int) $requestData['client_id'],
                  $requestData['payment_key']
        );

        if ($existing !== null) 
        {
            return $this->resolveExistingTransaction($existing);
        }

        $productData = $this->productRepository->getProductPrice($requestData['product_id']);
        $requestData['amount'] = $productData->amount * $requestData['quantity'];

        try 
        {
            $transaction = $this->transactionRepository->pendingTransaction($requestData);
        } 
        catch (QueryException $e) 
        {
            if (!$this->isUniqueConstraintViolation($e)) 
            {
                throw $e;
            }

            $existing = $this->transactionRepository->findByClientAndPaymentKey(
                (int) $requestData['client_id'],
                $requestData['payment_key']
            );

            if ($existing === null) 
            {
                throw $e;
            }

            return $this->resolveExistingTransaction($existing);
        }

        $paymentData = [
            'card_number' => $requestData['card_number'],
            'cvv'         => $requestData['cvv'],
        ];

        return $this->checkPaymentMethod($transaction, $paymentData);
    }

    private function resolveExistingTransaction(Transaction $existing): StoreTransactionResult
    {
        if ($existing->status === 'pending') {
            return StoreTransactionResult::pendingConflict($existing);
        }

        return StoreTransactionResult::idempotentReplay($existing);
    }

    private function isUniqueConstraintViolation(QueryException $e): bool
    {
        $sqlState = $e->errorInfo[0] ?? null; 

        if ($sqlState === '23000') // 2300 is the SQL state for unique constraint violation
        { 
            return true;
        }

        $message = $e->getMessage(); 

        return str_contains($message, 'UNIQUE constraint failed')
            || str_contains($message, 'Duplicate entry')
            || str_contains($message, 'unique constraint');
    }

    private function checkPaymentMethod(Transaction $transaction, array $paymentData): StoreTransactionResult
    {
        $paymentMethod = $transaction->payment_method;

        return match ($paymentMethod) 
        {
            'card_credit', 'card_debit' => $this->processCardPayment($transaction, $paymentData),
            default => throw new \InvalidArgumentException('Invalid payment method.'),
        };
    }

    private function processCardPayment(Transaction $transaction, array $paymentData): StoreTransactionResult
    {
        $activeGateways = $this->gatewayConfigurationService->getActivesGatewaysOrderByPriority();
        $lastError = null;

        foreach ($activeGateways as $gateway) 
        {
            try 
            {
                $gatewayService = match ((int) $gateway->id) 
                {
                    1 => $this->bearerTokenGatewayService, // Gateway 1 is the Bearer Token Gateway
                    2 => $this->headerAuthGatewayService, // Gateway 2 is the Header Auth Gateway
                    default => throw new \Exception('Gateways not found.', 400),
                };

                $paymentResponse = $gatewayService->processPayment($transaction, $paymentData);

                if ($paymentResponse) 
                {
                    $transaction->external_id = $paymentResponse['id'];
                    $transaction->gateway_id = $gateway->id;

                    DB::transaction(function () use ($transaction) 
                    {
                        $this->transactionRepository->successTransaction($transaction);
                        $this->purchaseRepository->store($transaction);
                    });

                    return StoreTransactionResult::created($transaction->fresh());
                }
            } 
            catch (\Exception $e) 
            {
                $lastError = $e;
            }
        }

        $this->transactionRepository->failedTransaction($transaction);

        if ($lastError) {
            throw new \Exception('Payment system unavailable', 500);
        }

        throw new \Exception('Payment rejected by all available gateways.', 400);
    }

    public function processRefund(int $id): RefundTransactionResult
    {
        $transaction = $this->transactionRepository->find($id);

        if ($transaction->status !== 'completed') 
        {
            throw new \Exception('Just transactions completed can be refunded.', 422);
        }

        $gatewayService = match ((int) $transaction->gateway_id) 
        {
            1 => $this->bearerTokenGatewayService, // Gateway 1 is the Bearer Token Gateway
            2 => $this->headerAuthGatewayService, // Gateway 2 is the Header Auth Gateway
            default => throw new \Exception('Gateway not found for this transaction.', 400),
        };

        try 
        {
            $refundResponse = $gatewayService->refund($transaction);

            if ($refundResponse) 
            {
                DB::transaction(function () use ($transaction) 
                {
                    $this->transactionRepository->refundTransaction($transaction);
                    $this->purchaseRepository->updateRefund($transaction->id);
                });

                return RefundTransactionResult::success();
            }

            throw new \Exception('Gateway refused the refund.', 400);
        } 
        catch (\Exception $e) 
        {
            throw new \Exception('Gateway Error', 500);
        }
    }
}
