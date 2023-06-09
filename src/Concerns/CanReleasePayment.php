<?php

namespace XtendLunar\Addons\PaymentGatewayStripe\Concerns;

use Illuminate\Support\Facades\DB;
use Lunar\Base\DataTransferObjects\PaymentAuthorize;

trait CanReleasePayment
{
    /**
     * Return a successfully released payment.
     *
     * @return void
     */
    private function releaseSuccess(): PaymentAuthorize
    {
        DB::transaction(function () {
            // Get our first successful charge.
            $charges = $this->paymentIntent->charges->data;

            $successCharge = collect($charges)->first(function ($charge) {
                return ! $charge->refunded && ($charge->status == 'succeeded' || $charge->status == 'paid');
            });

            $this->order->update([
                'status' => $this->config['released'] ?? 'paid',
                'placed_at' => now()->parse($successCharge->created),
            ]);

            $transactions = [];

            $type = 'capture';
            // if ($this->policy == 'manual') {
            //     $type = 'intent';
            // }

            foreach ($charges as $charge) {
                $card = $charge->payment_method_details->card;
                $transactions[] = [
                    'success' => $charge->status != 'failed',
                    'type' => $charge->amount_refunded ? 'refund' : $type,
                    'driver' => 'stripe',
                    'amount' => $charge->amount,
                    'reference' => $this->paymentIntent->id,
                    'status' => $charge->status,
                    'notes' => $charge->failure_message,
                    'card_type' => $card->brand,
                    'last_four' => $card->last4,
                    'captured_at' => $charge->amount_captured ? now() : null,
                    'meta' => [
                        'address_line1_check' => $card->checks->address_line1_check,
                        'address_postal_code_check' => $card->checks->address_postal_code_check,
                        'cvc_check' => $card->checks->cvc_check,
                    ],
                ];
            }
            $this->order->transactions()->createMany($transactions);
        });

        return new PaymentAuthorize(success: true);
    }
}
