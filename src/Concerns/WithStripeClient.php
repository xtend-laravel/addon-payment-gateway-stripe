<?php

namespace XtendLunar\Addons\PaymentGatewayStripe\Concerns;

use Illuminate\Support\Facades\App;
use Stripe\StripeClient;
use XtendLunar\Addons\PaymentGatewayStripe\Base\StripeConnectInterface;

trait WithStripeClient
{
    protected static StripeClient $stripe;

    protected static function stripeClient(): void
    {
        static::$stripe = App::make(StripeConnectInterface::class);
    }
}
