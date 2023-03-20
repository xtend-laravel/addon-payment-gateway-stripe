<?php

namespace XtendLunar\Addons\PaymentGatewayStripe\Restify;

use XtendLunar\Addons\PaymentGateways\Restify\PaymentGatewayRepository;
use XtendLunar\Addons\PaymentGatewayStripe\Restify\Presenters\StripePresenter;

class StripeRepository extends PaymentGatewayRepository
{
    public static string $presenter = StripePresenter::class;
}
