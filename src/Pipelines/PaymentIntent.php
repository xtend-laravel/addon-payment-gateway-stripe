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
        // Ignores current cart getter request
        if (request()->route()->parameter('getter') === 'current-cart') {
            return $next($cart);
        }

        $this->initStripe();
        $shipping = $cart->shippingAddress;

        if ($cart->total->value <= 0) {
            return $next($cart);
        }

        $paymentIntent = $this->updateOrCreatePaymentIntent($cart, collect([
            'amount' => $cart->total->value,
            'currency' => $cart->currency->code,
            'payment_method_types' => ['card'],
            'capture_method' => config('stripe.capture_method'),
            'shipping' => $shipping ? [
                'name' => "{$shipping->first_name} {$shipping->last_name}",
                'address' => [
                    'city' => $shipping->city,
                    'country' => $shipping->country?->iso2,
                    'line1' => $shipping->line_one,
                    'line2' => $shipping->line_two,
                    'postal_code' => $shipping->postcode,
                    'state' => $shipping->state,
                ]
            ] : [],
        ]));

        $cart->update([
            'meta' => collect($cart->meta ?? [])->merge([
                'stripe_payment_intent' => $paymentIntent->id,
                'stripe_client_secret' => $paymentIntent->client_secret,
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
