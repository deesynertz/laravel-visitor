<?php

namespace Deesynertz\Visitor;

use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;
use Deesynertz\Visitor\Commands\PropertyVisitorCommand;

class VisitorServiceProvider extends ServiceProvider
{

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([PropertyVisitorCommand::class,]);
        }

        Schema::defaultStringLength(191);

        // $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        // $this->loadViewsFrom(__DIR__.'/./../resources/views', 'views');
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->registerPublishables();
    }

    private function registerPublishables() 
    {
        $basePath = __DIR__;
        $arrPublishable = [
            'deesynertz-visitor-migrations' => [
                "$basePath/publishable/database/migrations" => database_path('migrations'),
            ],
            'deesynertz-visitor-config' => [
                "$basePath/publishable/config/property-visitor.php" => config_path('property-visitor.php'),
            ]
        ];

        foreach ($arrPublishable as $group => $paths) {
            $this->publishes($paths, $group);
        }
    }
}
