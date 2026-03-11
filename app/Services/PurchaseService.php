<?php

namespace App\Services;

use App\Models\Product;
use App\Models\Transaction;

class PurchaseService
{
    public function store($requestData)
    {
        $productData = $this->getProdutAndPrice($requestData['product_id']);

        $transaction = Transaction::create([
            'customer_id'       => $requestData['customer_id'],
            'product_id'        => $requestData['product_id'],
            'amount'            => $productData['price'],
            'method'            => $requestData['payment_method'],
            'card_last_numbers' => $requestData['card_last_numbers'] ?? null,
            'status'            => 'pending',
        ]);

        return $this->checkPaymentMethod(
            $requestData['payment_method'],
         $requestData['card_last_numbers'] ?? null,
         $transaction
        );
    }

    private function getProdutAndPrice($productId)
    {
        $product = Product::findOrFail($productId);
        $price   = $product->price;

        return [
            'product' => $product,
            'price'   => $price
        ];
    }

    private function checkPaymentMethod($paymentMethod, $cardLastNumbers, $transaction)
    {
        return match ($paymentMethod) 
        {
            'card_credit', 'card_debit' => $this->processCardPayment($transaction,$cardLastNumbers),
            'pix'                       => $this->processPixPayment($transaction),
            'boleto'                    => $this->processBoletoPayment($transaction),
            default                     => throw new \InvalidArgumentException('Invalid payment method.'),
        };
    }

    private function processCardPayment($transaction,$cardLastNumbers)
    {
        // Simulate card payment processing
        return true;
    }

    private function processPixPayment($transaction)
    {
        // Simulate Pix payment processing
        return true;
    }

    private function processBoletoPayment($transaction)
    {
        // Simulate boleto payment processing
        return true;
    }

}