<?php

namespace App\Services;

use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\TransactionRepositoryInterface;
use App\Models\Transaction;
use App\Services\Gateways\BearerTokenGatewayService;
use App\Services\Gateways\GatewayConfigurationService;
use App\Services\Gateways\HeaderAuthGatewayService;

class TransactionService
{
    public function __construct(
       protected TransactionRepositoryInterface $transactionRepository,
       protected ProductRepositoryInterface     $productRepository,
       protected PurchaseRepository             $purchaseRepository,
       protected BearerTokenGatewayService      $bearerTokenGatewayService,
       protected HeaderAuthGatewayService       $headerAuthGatewayService,
       protected GatewayConfigurationService    $gatewayConfigurationService
    ){}
    
    public function store($requestData)
    {
        $productData = $this->productRepository->getProductPrice($requestData['product_id']);
        
        $requestData['amount'] = $productData->amount * $requestData['quantity'];
        
        $transaction = $this->transactionRepository->pendingTransaction($requestData);

        $paymentData = [
            'card_number' => $requestData['card_number'],   // just card
            'cvv'         => $requestData['cvv'],
        ];

        return $this->checkPaymentMethod($transaction, $paymentData);
    }

    private function checkPaymentMethod($transaction, $paymentData)
    {
        $paymentMethod = $transaction->payment_method;
       
        return match ($paymentMethod) 
        {
            'card_credit', 'card_debit' => $this->processCardPayment($transaction, $paymentData),
            // 'pix'                    => $this->processPixPayment($transaction),
            // 'boleto'                 => $this->processBoletoPayment($transaction),
            default                     => throw new \InvalidArgumentException('Invalid payment method.'),
        };
    }

    private function processCardPayment(Transaction $transaction, array $paymentData)
    {
        try {
            $activeGateways = $this->gatewayConfigurationService->getActivesGatewaysOrderByPriority();

            foreach ($activeGateways as $gateway) 
            {
                $gatewayService = match ((int) $gateway->id) {
                    1 => $this->bearerTokenGatewayService,
                    2 => $this->headerAuthGatewayService,
                    default => throw new \Exception("Gateways not found.", 400),
                };

                $paymentResponse = $gatewayService->processPayment($transaction, $paymentData);

                if ($paymentResponse) {
                    $transaction->external_id = $paymentResponse['id'];
                    $transaction->gateway_id  = $gateway->id;
                    
                    $this->transactionRepository->successTransaction($transaction);

                    $this->purchaseRepository->store($transaction);

                    return true;
                }
            }

            $this->transactionRepository->failedTransaction($transaction);
            throw new \Exception('Payment rejected by all available gateways.', 400);

        } catch (\Exception $e) {

            $this->transactionRepository->failedTransaction($transaction);
            throw new \Exception('Payment system unavailable', 500);
        }
    }

    public function processRefund($id)
    {
        $transaction = $this->transactionRepository->find($id);

        if ($transaction->status !== 'completed') {
            throw new \Exception("Just transactions completed can be refunded.", 422);
        }

        $gatewayService = match ((int) $transaction->gateway_id) {
            1 => $this->bearerTokenGatewayService,
            2 => $this->headerAuthGatewayService,
            default => throw new \Exception("Gateway not found for this transaction.", 400),
        };

        try {
            
            $refundResponse = $gatewayService->refund($transaction);

            if ($refundResponse) 
            {
                $this->transactionRepository->refundTransaction($transaction);

                $this->purchaseRepository->updateRefund($transaction->id);
                
                return true;
            }

            throw new \Exception('Gateway refused the refund.', 400);

        } catch (\Exception $e) {
            throw new \Exception('Gateway Error', 500);
        }
    }


    // private function processPixPayment($transaction)
    // {

    //     return true;
    // }

    // private function processBoletoPayment($transaction)
    // {

    //     return true;
    // }

}