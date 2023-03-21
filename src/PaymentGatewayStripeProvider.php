<?php

namespace XtendLunar\Addons\PaymentGatewayStripe;

use Binaryk\LaravelRestify\Traits\InteractsWithRestifyRepositories;
use CodeLabX\XtendLaravel\Base\XtendAddonProvider;
use Illuminate\Support\Facades\Blade;
use Lunar\Facades\Payments;
use Stripe\StripeClient;
use Xtend\Extensions\Lunar\Core\Concerns\XtendLunarCartPipeline;
use XtendLunar\Addons\PaymentGatewayStripe\Base\StripeConnectInterface;
use XtendLunar\Addons\PaymentGatewayStripe\Base\StripePayment;
use XtendLunar\Addons\PaymentGatewayStripe\Pipelines\PaymentIntent;

class PaymentGatewayStripeProvider extends XtendAddonProvider
{
    use InteractsWithRestifyRepositories;
    use XtendLunarCartPipeline;

    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'xtend-lunar::payment-gateway-stripe');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'xtend-lunar::payment-gateway-stripe');
        $this->loadRestifyFrom(__DIR__.'/Restify', __NAMESPACE__.'\\Restify\\');
        $this->registerWithCartPipeline([PaymentIntent::class]);
    }

    public function boot()
    {
        Blade::componentNamespace('XtendLunar\\Addons\\PaymentGatewayStripe\\Components', 'xtend-lunar::payment-gateway-stripe');

        Payments::extend('stripe', function ($app) {
            return $app->make(StripePayment::class);
        });

        $this->app->singleton(StripeConnectInterface::class, function ($app) {
            return new StripeClient(config('services.stripe.key'));
        });
    }
}
