<?php

namespace XtendLunar\Addons\PaymentGatewayStripe\Base;

use Lunar\PaymentTypes\AbstractPayment;
use XtendLunar\Addons\PaymentGatewayStripe\Concerns\CanAuthorizePayment;
use XtendLunar\Addons\PaymentGatewayStripe\Concerns\CanCapturePayment;
use XtendLunar\Addons\PaymentGatewayStripe\Concerns\CanRefundPayment;
use XtendLunar\Addons\PaymentGatewayStripe\Concerns\WithStripeClient;
use XtendLunar\Features\PaymentGateways\Contracts\OnlinePaymentGateway;

class StripePayment extends AbstractPayment implements OnlinePaymentGateway
{
    use WithStripeClient;
    use CanAuthorizePayment;
    use CanCapturePayment;
    use CanRefundPayment;

    public function handle()
    {
        // Check if we have a valid order in the cart
        if (! $this->cart->hasOrder()) {
            return;
        }

        // Get the order
        $this->order = $this->cart->order;
    }
}
