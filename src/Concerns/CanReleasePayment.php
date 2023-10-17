<?php

namespace XtendLunar\Addons\PaymentGatewayStripe\Concerns;

use Lunar\Base\DataTransferObjects\PaymentAuthorize;

trait CanReleasePayment
{
    use WithStripeClient;

    /**
     * Return a successfully released payment.
     *
     * @return void
     */
    private function releaseSuccess(): PaymentAuthorize
    {
        $chargeId = $this->paymentIntent->latest_charge;

        $charge = static::$stripe->charges->retrieve(
            $chargeId,
            static::withStripeHeaders(),
        );

        if (!$charge) {
            return new PaymentAuthorize(
                success: false,
                message: 'No charges found so can not make any transactions',
            );
        }

        $successCharge =  ! $charge->refunded && ($charge->status == 'succeeded' || $charge->status == 'paid');
        if (! $successCharge) {
            return new PaymentAuthorize(
                success: false,
                message: 'No successful charges found so can not make any transactions',
            );
        }

        $this->order->update([
            'status' => $this->config['released'] ?? 'payment-received',
            'placed_at' => now()->parse($charge->created),
        ]);

        $type = 'capture';
        $card = $charge->payment_method_details->card;
        $transaction = [
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

        $this->order->transactions()->create($transaction);

        return new PaymentAuthorize(success: true);
    }
}
