<?php

namespace XtendLunar\Addons\PaymentGatewayStripe\Concerns;

use Lunar\Base\DataTransferObjects\PaymentCapture;
use Lunar\Models\Transaction;

trait CanCapturePayment
{
    public function capture(Transaction $transaction, $amount = 0): PaymentCapture
    {
        return new PaymentCapture($this);
    }
}
