<?php

namespace App\Services;

use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\TransactionRepositoryInterface;
use App\Models\Transaction;
use App\Services\Gateway1Service;
use App\Services\Gateway2Service;

class PurchaseService
{
    public function __construct(
       protected TransactionRepositoryInterface $transactionRepository,
       protected ProductRepositoryInterface $productRepository,
       protected Gateway1Service $gateway1Service,
       protected Gateway2Service $gateway2Service
    ){}
    
    public function store($requestData)
    {
        $productData = $this->productRepository->getProductAndPrice($requestData['product_id']);
        
        $requestData['amount']  = $productData['amount'];
        $requestData['product'] = $productData['product'];
        
        $transaction = $this->transactionRepository->pendingTransaction($requestData);
        
        return $this->checkPaymentMethod($transaction);
    }

    private function checkPaymentMethod($transaction)
    {
        $paymentMethod = $transaction->payment_method;
       
        return match ($paymentMethod) 
        {
            'card_credit', 'card_debit' => $this->processCardPayment($transaction),
            'pix'                       => $this->processPixPayment($transaction),
            'boleto'                    => $this->processBoletoPayment($transaction),
            default                     => throw new \InvalidArgumentException('Invalid payment method.'),
        };
    }

    private function processCardPayment(Transaction $transaction)
    {
        try {
            $gatewaysToTry = [
                1 => $this->gateway1Service,
                2 => $this->gateway2Service,
            ];

            foreach ($gatewaysToTry as $gatewayId => $gatewayService) 
            {
                if ($gatewayService->processPayment($transaction)) 
                {
                    $transaction['gateway_id'] = $gatewayId;

                    $this->transactionRepository->successTransaction($transaction, $gatewayId);
                    return true;
                }
            }

            $this->transactionRepository->failedTransaction($transaction);
            throw new \Exception('Payment rejected by gateways.', 400);

        } catch (\Exception $e) {

            $this->transactionRepository->failedTransaction($transaction);
            throw new \Exception('Payment system unavailable.', 500);
        }
    }

    public function processRefund(Transaction $transaction)
    {
        if ($transaction->status !== 'completed') {
            throw new \Exception("Just transactions completed can be refund", 422);
        }

        $gatewayService = match ($transaction->gateway_id) 
        {
            1 => $this->gateway1Service,
            2 => $this->gateway2Service,
            default => throw new \Exception("Gateway not found", 400),
        };

        try {
            $isRefunded = $gatewayService->refund($transaction);

            if ($isRefunded) 
            {
                $this->transactionRepository->refundTransaction($transaction);
                return true;
            }

            throw new \Exception('Gateway recused', 400);

        } catch (\Exception $e) {
            throw new \Exception('Error gateway: ' . $e->getMessage(), 500);
        }
    }


    private function processPixPayment($transaction)
    {
        $cardLastNumbers = $transaction->card_last_numbers;

        return true;
    }

    private function processBoletoPayment($transaction)
    {
        // Simulate boleto payment processing
        return true;
    }

}