<?php

namespace XtendLunar\Addons\PaymentGatewayStripe;

use Binaryk\LaravelRestify\Traits\InteractsWithRestifyRepositories;
use CodeLabX\XtendLaravel\Base\XtendAddonProvider;
use Illuminate\Support\Facades\Blade;

class PaymentGatewayStripeProvider extends XtendAddonProvider
{
    use InteractsWithRestifyRepositories;

    public function register()
    {
        $this->loadViewsFrom(__DIR__.'/../resources/views', 'xtend-lunar::payment-gateway-stripe');
        $this->loadTranslationsFrom(__DIR__.'/../resources/lang', 'xtend-lunar::payment-gateway-stripe');
        $this->loadRestifyFrom(__DIR__.'/Restify', __NAMESPACE__.'\\Restify\\');
    }

    public function boot()
    {
        Blade::componentNamespace('XtendLunar\\Addons\\PaymentGatewayStripe\\Components', 'xtend-lunar::payment-gateway-stripe');
    }
}
