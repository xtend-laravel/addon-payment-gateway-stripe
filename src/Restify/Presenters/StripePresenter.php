<?php

namespace XtendLunar\Addons\PaymentGatewayStripe\Restify\Presenters;

use XtendLunar\Addons\PaymentGateways\Restify\Presenters\PaymentGatewayPresenter;
use XtendLunar\Addons\RestifyApi\Restify\Contracts\Presentable;
use Binaryk\LaravelRestify\Http\Requests\RestifyRequest;

class StripePresenter extends PaymentGatewayPresenter implements Presentable
{
    public function transform(RestifyRequest $request): array
    {
        return $this->data;
    }
}
