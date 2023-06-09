<?php

namespace XtendLunar\Addons\PaymentGatewayStripe\Concerns;

use Lunar\Base\DataTransferObjects\PaymentAuthorize;

trait CanAuthorizePayment
{
    use CanReleasePayment;

    public function authorize(): PaymentAuthorize
    {
        return new PaymentAuthorize(true);
    }
}
