<?php

namespace Timmoh\MailcoachRssReader;

use Illuminate\Support\ServiceProvider;

class MailcoachRssReaderServiceProvider extends ServiceProvider {

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = false;

    /**
     * Bootstrap the service provider.
     *
     * @return void
     */
    public function boot() {
        $this->publishes([
            __DIR__ . '/../config/mailcoach-rss-reader.php' => config_path('mailcoach-rss-reader.php'),
        ],
            'mailcoach-rss-reader-config');
    }

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register() {
        // Bind to the "Asset" section
        $this->app->bind('mailcoach-rss-reader',
            function ($app) {
                return new MailcoachRssReader($app);
            });

        $this->mergeConfigFrom(
            __DIR__ . '/../config/mailcoach-rss-reader.php',
            'mailcoach-rss-reader'
        );
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides() {
        return ['mailcoach-rss-reader'];
    }
}
