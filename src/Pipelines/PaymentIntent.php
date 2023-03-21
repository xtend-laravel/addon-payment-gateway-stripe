<?php

namespace XtendLunar\Addons\PaymentGatewayStripe\Pipelines;

use Closure;
use Lunar\DataTypes\Price;
use Lunar\Models\Cart;

class PaymentIntent
{
    /**
     * Called after cart totals have been calculated.
     *
     * @return void
     */
    public function handle(Cart $cart, Closure $next)
    {
        $discountTotal = $cart->lines->sum('discountTotal.value');

        $subTotal = $cart->lines->sum('subTotal.value') - $discountTotal;
        $total = $cart->lines->sum('total.value');

        // Get the shipping address
        if ($shippingAddress = $cart->shippingAddress) {
            if ($shippingAddress->shippingSubTotal) {
                $subTotal += $shippingAddress->shippingSubTotal?->value;
                $total += $shippingAddress->shippingTotal?->value;
            }
        }

        $cart->subTotal = new Price($subTotal, $cart->currency, 1);
        $cart->discountTotal = new Price($discountTotal, $cart->currency, 1);
        $cart->total = new Price($total, $cart->currency, 1);

        // \Stripe\PaymentIntent::create([
        //     'amount' => $cart->total->value,
        //     'currency' => $cart->currency->code,
        //     'payment_method_types' => ['card'],
        //     'capture_method' => 'manual',
        //     'shipping' => $this->getShippingAddress($cart),
        // ]);

        return $next($cart);
    }

    protected function getShippingAddress(Cart $cart)
    {
        if (! $cart->shippingAddress) {
            return;
        }

        return [
            'name' => $cart->shippingAddress->name,
            'address' => [
                'line1' => $cart->shippingAddress->address1,
                'line2' => $cart->shippingAddress->address2,
                'city' => $cart->shippingAddress->city,
                'state' => $cart->shippingAddress->state,
                'postal_code' => $cart->shippingAddress->zip,
                'country' => $cart->shippingAddress->country,
            ],
        ];
    }
}
