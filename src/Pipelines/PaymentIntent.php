<?php

namespace XtendLunar\Addons\PaymentGatewayStripe\Pipelines;

use Closure;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
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
        if (request()->has('action') && request()->action === 'create-order-action') {
            return $next($cart);
        }

        if (request()->route()->parameter('getter') === 'current-cart') {
            return $next($cart);
        }

        $this->initStripe();
        $shipping = $cart->shippingAddress;

        if ($cart->total->value <= 0 && $cart->lines->isEmpty()) {
            return $next($cart);
        }

        $giftWrapFee = $cart->total->value === 0 ? 50 : 0;
        $shippingTotal = $cart->shippingTotal->value ?? 0;
        $amount = $cart->total->value + $shippingTotal + $giftWrapFee;
        $paymentIntent = $this->updateOrCreatePaymentIntent($cart, collect([
            'amount' => $amount,
            'currency' => $cart->currency->code,
            'automatic_payment_methods' => [
                'enabled' => true,
            ],
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

    protected function updateOrCreatePaymentIntent(Cart $cart, Collection $params): \Stripe\PaymentIntent
    {
        // if ($cart->meta->stripe_payment_intent ?? null) {
        //     return static::$stripe->paymentIntents->update($cart->meta->stripe_payment_intent, $params);
        // }

        return static::$stripe->paymentIntents->create(
            params: $params->toArray(),
            opts: $this->idempotencyKeyHeader('cart-'.$cart->id.md5($params)),
        );
    }
}
