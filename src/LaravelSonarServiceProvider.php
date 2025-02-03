<?php

namespace Mafrasil\LaravelSonar;

use Mafrasil\LaravelSonar\Commands\ExportReactCommand;
use Mafrasil\LaravelSonar\Commands\LaravelSonarCommand;
use Mafrasil\LaravelSonar\View\Components\SonarScripts;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class LaravelSonarServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        $package
            ->name('laravel-sonar')
            ->hasConfigFile()
            ->hasViews()
            ->hasMigration('create_sonar_events_table')
            ->hasCommands([
                LaravelSonarCommand::class,
                ExportReactCommand::class,
            ])
            ->hasRoute('api')
            ->hasViewComponent('sonar', SonarScripts::class)
            ->hasAssets();
    }

    public function packageBooted()
    {
        $this->app['router']->pushMiddlewareToGroup('web', \Mafrasil\LaravelSonar\Http\Middleware\InjectSonarScripts::class);

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'sonar-migrations');
        }
    }
}
