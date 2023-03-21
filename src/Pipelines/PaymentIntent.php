<?php

namespace XtendLunar\Addons\PaymentGatewayStripe\Pipelines;

use Closure;
use Lunar\Models\Cart;
use XtendLunar\Addons\PaymentGatewayStripe\Concerns\WithStripeClient;

class PaymentIntent
{
    use WithStripeClient;

    /**
     * Called after cart totals have been calculated.
     *
     * @return void
     */
    public function handle(Cart $cart, Closure $next)
    {
        $this->initStripe();
        $cart->update([
            'meta' => collect($cart->meta ?? [])->merge([
                'payment_intent' => $this->updateOrCreatePaymentIntent($cart, collect([
                    'amount' => $cart->total->value,
                    'currency' => $cart->currency->code,
                    'payment_method_types' => ['card'],
                    'capture_method' => config('stripe.capture_method'),
                ]))->id,
            ]),
        ]);

        return $next($cart);
    }

    protected function updateOrCreatePaymentIntent(Cart $cart, $params): \Stripe\PaymentIntent
    {
        if ($cart->meta->payment_intent ?? null) {
            return static::$stripe->paymentIntents->update($cart->meta->payment_intent, $params);
        }

        return static::$stripe->paymentIntents->create(
            params: $params,
            opts: $this->idempotencyKeyHeader('cart-'.$cart->id.md5($params)),
        );
    }
}
