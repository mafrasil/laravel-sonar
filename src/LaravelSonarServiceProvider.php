<?php

namespace Mafrasil\LaravelSonar;

use Illuminate\Support\Facades\Gate;
use Mafrasil\LaravelSonar\Commands\ExportReactCommand;
use Mafrasil\LaravelSonar\Commands\LaravelSonarCommand;
use Mafrasil\LaravelSonar\Commands\SonarStatsCommand;
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
                SonarStatsCommand::class,
            ])
            ->hasRoute('api')
            ->hasRoute('web')
            ->hasViewComponent('sonar', SonarScripts::class)
            ->hasAssets();
    }

    public function packageBooted()
    {
        $this->app['router']->pushMiddlewareToGroup('web', \Mafrasil\LaravelSonar\Http\Middleware\InjectSonarScripts::class);

        // Register gate for Sonar dashboard
        Gate::define('viewSonar', function ($user = null) {
            if (app()->environment('local')) {
                return true;
            }

            $emails = config('sonar.allowed_emails', []);
            return $user && in_array($user->email, $emails);
        });

        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../database/migrations' => database_path('migrations'),
            ], 'sonar-migrations');
        }
    }
}
