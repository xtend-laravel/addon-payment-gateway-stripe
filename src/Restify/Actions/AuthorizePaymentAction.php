<?php

namespace XtendLunar\Addons\PaymentGatewayStripe\Restify\Actions;

use Binaryk\LaravelRestify\Actions\Action;
use Binaryk\LaravelRestify\Http\Requests\ActionRequest;
use Illuminate\Http\JsonResponse;
use Xtend\Extensions\Lunar\Core\Models\Order;

class AuthorizePaymentAction extends Action
{
    public function handle(ActionRequest $request, Order $order): JsonResponse
    {
        return data([
            'order' => $order,
            'payment' => $order->payment,
            'payment_method' => $order->payment_method,
            'payment_method_type' => $order->payment_method_type,
            'payment_method_type_id' => $order->payment_method_type_id,
        ]);
    }
}
