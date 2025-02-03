<?php

namespace Mafrasil\LaravelSonar;

use Illuminate\Support\Facades\Gate;
use Mafrasil\LaravelSonar\Commands\ExportReactCommand;
use Mafrasil\LaravelSonar\Commands\LaravelSonarCommand;
use Mafrasil\LaravelSonar\Commands\SonarStatsCommand;
use Mafrasil\LaravelSonar\View\Components\SonarScripts;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
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
            ->discoversMigrations()
            ->hasCommands([
                LaravelSonarCommand::class,
                ExportReactCommand::class,
                SonarStatsCommand::class,
            ])
            ->hasRoute('api')
            ->hasRoute('web')
            ->hasViewComponent('sonar', SonarScripts::class)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->startWith(function (InstallCommand $command) {
                        $command->info('Installing Laravel Sonar...');
                    })
                    ->publishConfigFile()
                    ->publishAssets()
                    ->publishMigrations()
                    ->askToRunMigrations()
                    ->askToStarRepoOnGitHub('mafrasil/laravel-sonar')
                    ->endWith(function (InstallCommand $command) {
                        $command->info('Have a great day!');
                    });
            });
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
            // Config
            $this->publishes([
                __DIR__.'/../config/sonar.php' => config_path('sonar.php'),
            ], ['laravel-sonar', 'laravel-sonar-config']);

            // Views
            if (is_dir(__DIR__.'/../resources/views')) {
                $this->publishes([
                    __DIR__.'/../resources/views' => resource_path('views/vendor/sonar'),
                ], ['laravel-sonar', 'laravel-sonar-views']);
            }

            // Assets
            if (is_dir(__DIR__.'/../dist')) {
                $this->publishes([
                    __DIR__.'/../dist/sonar.iife.js' => public_path('vendor/sonar/sonar.js'), // Specific file with new name
                    // Add other dist files if needed
                ], 'laravel-sonar-assets');
            }

        }
    }
}
