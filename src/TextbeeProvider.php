<?php

namespace Mr3od\Textbee;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Support\ServiceProvider;
use Mr3od\Textbee\Exceptions\InvalidConfigException;

class TextbeeProvider extends ServiceProvider implements DeferrableProvider
{
    public function boot() {}

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/textbee-notification-channel.php',
            'textbee-notification-channel'
        );

        $this->publishes([
            __DIR__.'/../config/textbee-notification-channel.php' => config_path('textbee-notification-channel.php'),
        ]);

        $this->app->singleton(TextbeeConfig::class, function () {
            $app = $this->app;
            return new TextbeeConfig($app['config']['textbee-notification-channel']);
        });

        $this->app->singleton(TextbeeService::class, function (Application $app) {
            $config = $app->make(TextbeeConfig::class);

            if ($apiKey = $config->getApiKey()) {
                return new TextbeeService($apiKey, $config->getBaseUrl());
            }

            throw InvalidConfigException::missingConfig();
        });

        $this->app->singleton(Textbee::class, function (Application $app) {
            return new Textbee(
                $app->make(TextbeeService::class),
                $app->make(TextbeeConfig::class)
            );
        });

        $this->app->singleton(TextbeeChannel::class, function (Application $app) {
            return new TextbeeChannel(
                $app->make(Textbee::class),
                $app->make(Dispatcher::class)
            );
        });
    }

    public function provides(): array
    {
        return [
            TextbeeChannel::class,
            TextbeeConfig::class,
            TextbeeService::class,
        ];
    }
}
