<?php

namespace Mwc\Generators;

use Illuminate\Support\ServiceProvider;

class GeneratorsServiceProvider extends ServiceProvider {


    /**
     * Booting
     */
    public function boot()
    {

        $viewPath = __DIR__ . '/../resources/views';

        $this->loadViewsFrom($viewPath, 'laravel-generators');

        $this->publishes([
            $viewPath => config('view.paths')[0] . '/vendor/laravel-generators',
        ], 'views');

        $this->publishes([
            __DIR__.'/../config/generators.php' => config_path('generators.php'),
        ], 'config');
    }

	/**
	 * Register the commands
	 *
	 * @return void
	 */
	public function register()
	{
        foreach([
            'Controller',
            'Model',
            'Migration',
            'View',
        ] as $command)
        {
            $this->{"register$command"}();
        }

        $this->app->register('Mwc\Generators\Providers\BladeServiceProvider');
	}

    /**
     * Register the controller generator
     */
    protected function registerController()
    {
        $this->app->singleton('generate.controller', function ($app) {
            return $app['Mwc\Generators\Console\ControllerCommand'];
        });

        $this->commands('generate.controller');
    }

    /**
     * Register the model generator
     */
    protected function registerModel()
    {
        $this->app->singleton('generate.model', function ($app) {
            return $app['Mwc\Generators\Console\ModelCommand'];
        });

        $this->commands('generate.model');
    }

    /**
     * Register the migration generator
     */
    protected function registerMigration()
    {
        $this->app->singleton('generate.migration', function ($app) {
            return $app['Mwc\Generators\Console\MigrationCommand'];
        });

        $this->commands('generate.migration');
    }

    /**
     * Register the view generator
     */
    protected function registerView()
    {
        $this->app->singleton('generate.view', function ($app) {
            return $app['Mwc\Generators\Console\ViewCommand'];
        });

        $this->commands('generate.view');
    }
}
