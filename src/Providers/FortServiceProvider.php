<?php

declare(strict_types=1);

namespace Cortex\Fort\Providers;

use Rinvex\Fort\Contracts\RoleContract;
use Rinvex\Fort\Contracts\UserContract;
use Illuminate\Support\ServiceProvider;
use Cortex\Fort\Console\Commands\SeedCommand;
use Cortex\Fort\Console\Commands\InstallCommand;
use Cortex\Fort\Console\Commands\MigrateCommand;
use Cortex\Fort\Console\Commands\PublishCommand;

class FortServiceProvider extends ServiceProvider
{
    /**
     * The commands to be registered.
     *
     * @var array
     */
    protected $commands = [
        MigrateCommand::class => 'command.cortex.fort.migrate',
        PublishCommand::class => 'command.cortex.fort.publish',
        InstallCommand::class => 'command.cortex.fort.install',
        SeedCommand::class => 'command.cortex.fort.seed',
    ];

    /**
     * Register any application services.
     *
     * This service provider is a great spot to register your various container
     * bindings with the application. As you can see, we are registering our
     * "Registrar" implementation here. You can add your own bindings too!
     *
     * @return void
     */
    public function register()
    {
        // Register console commands
        ! $this->app->runningInConsole() || $this->registerCommands();
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Load resources
        require __DIR__.'/../../routes/breadcrumbs.php';
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.frontend.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.userarea.php');
        $this->loadRoutesFrom(__DIR__.'/../../routes/web.backend.php');
        $this->loadViewsFrom(__DIR__.'/../../resources/views', 'cortex/fort');
        $this->loadTranslationsFrom(__DIR__.'/../../resources/lang', 'cortex/fort');
        $this->app->afterResolving('blade.compiler', function () {
            require __DIR__.'/../../routes/menus.php';
        });

        // Publish Resources
        ! $this->app->runningInConsole() || $this->publishResources();

        // Register attributable entities
        app('rinvex.attributable.entities')->push(RoleContract::class);
        app('rinvex.attributable.entities')->push(UserContract::class);
    }

    /**
     * Publish resources.
     *
     * @return void
     */
    protected function publishResources()
    {
        $this->publishes([realpath(__DIR__.'/../../resources/lang') => resource_path('lang/vendor/cortex/fort')], 'cortex-fort-lang');
        $this->publishes([realpath(__DIR__.'/../../resources/views') => resource_path('views/vendor/cortex/fort')], 'cortex-fort-views');
    }

    /**
     * Register console commands.
     *
     * @return void
     */
    protected function registerCommands()
    {
        // Register artisan commands
        foreach ($this->commands as $key => $value) {
            $this->app->singleton($value, function ($app) use ($key) {
                return new $key();
            });
        }

        $this->commands(array_values($this->commands));
    }
}
