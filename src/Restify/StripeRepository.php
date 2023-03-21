<?php

namespace XtendLunar\Addons\PaymentGatewayStripe\Restify;

use XtendLunar\Addons\PaymentGatewayStripe\Restify\Presenters\StripePresenter;
use XtendLunar\Addons\RestifyApi\Restify\Repository;

class StripeRepository extends Repository
{
    public static string $presenter = StripePresenter::class;
}
