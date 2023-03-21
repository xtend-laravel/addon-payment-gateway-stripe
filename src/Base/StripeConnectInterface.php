<?php

namespace XtendLunar\Addons\PaymentGatewayStripe\Base;

use Stripe\StripeClient;

/** @mixin StripeClient */
interface StripeConnectInterface
{
    public function createCustomer(string $email): string;

    public function createProduct(string $name): string;

    public function createPrice(string $productId, int $amount): string;

    public function createCheckoutSession(array $data): string;
}
