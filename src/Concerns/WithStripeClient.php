<?php

namespace XtendLunar\Addons\PaymentGatewayStripe\Concerns;

use Illuminate\Support\Facades\App;
use Stripe\StripeClient;
use XtendLunar\Addons\PaymentGatewayStripe\Base\StripeConnectInterface;

trait WithStripeClient
{
    protected static StripeClient $stripe;

    protected static function initStripe(): void
    {
        static::$stripe = App::make(StripeConnectInterface::class);
    }

    protected static function withStripeHeaders(array $headers = [], ?string $idempotencyKey = null): array
    {
        return array_merge($headers, static::idempotencyKeyHeader($idempotencyKey));
    }

    protected static function idempotencyKeyHeader(?string $idempotencyKey): array
    {
        if (!$idempotencyKey) {
            return [];
        }

        return [
            'idempotency_key' => $idempotencyKey,
        ];
    }
}
