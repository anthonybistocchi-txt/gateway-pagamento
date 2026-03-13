<?php

namespace App\Services;

use App\Interfaces\ClientRepositoryInterface;
use App\Interfaces\ProductRepositoryInterface;
use App\Interfaces\TransactionRepositoryInterface;
use App\Models\Transaction;
use App\Services\Gateway1Service;
use App\Services\Gateway2Service;

class PurchaseService
{
    public function __construct(
       protected ClientRepositoryInterface $clientRepository,
       protected TransactionRepositoryInterface $transactionRepository,
       protected ProductRepositoryInterface $productRepository,
       protected Gateway1Service $gateway1Service,
       protected Gateway2Service $gateway2Service
    ){}
    
    public function store($requestData)
    {
        $productData = $this->productRepository->getProductPrice($requestData['product_id']);
        
        $requestData['amount'] = $productData->amount;
        
        $transaction = $this->transactionRepository->pendingTransaction($requestData);

        $paymentData = [
            'card_number' => $requestData['card_number'],   // apenas cartao
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
            'pix'                       => $this->processPixPayment($transaction),
            'boleto'                    => $this->processBoletoPayment($transaction),
            default                     => throw new \InvalidArgumentException('Invalid payment method.'),
        };
    }

    private function processCardPayment(Transaction $transaction, array $paymentData)
    {
        try {

            $gatewaysToTry = [
                1 => $this->gateway1Service,
                2 => $this->gateway2Service,
            ];

            foreach ($gatewaysToTry as $gatewayId => $gatewayService) 
            {
                $paymentResponse = $gatewayService->processPayment($transaction, $paymentData);

                if ($paymentResponse) 
                {                      
                    $transaction->external_id = $paymentResponse['id']; 
                    $transaction->gateway_id  = $gatewayId;
                    
                    $this->transactionRepository->successTransaction($transaction);

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