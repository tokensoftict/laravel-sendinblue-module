<?php
namespace TokenSoft\LaravelSendinBlue;

use TokenSoft\LaravelSendinBlue;
use TokenSoft\LaravelSendinBlue\Transport\SendinBlueAddedTransportManager;
use Illuminate\Mail\MailServiceProvider;
use Illuminate\Support\ServiceProvider;


class SendinBlueServiceProvider extends MailServiceProvider
{

    public function boot()
    {
        $this->publishes([__DIR__.'/config/tokensendinblue.php' => config_path('tokensendinblue.php')], 'tokensendinblue');
    }

    protected function registerSwiftTransport()
    {
        $this->app->singleton('swift.transport', function ($app) {
            return new SendinBlueAddedTransportManager($app);
        });
    }

}
