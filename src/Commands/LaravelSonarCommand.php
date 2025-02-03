<?php

namespace Mafrasil\LaravelSonar\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class LaravelSonarCommand extends Command
{
    public $signature = 'sonar:publish-assets';

    public $description = 'Publish the Sonar JavaScript assets';

    public function handle(): int
    {
        $this->comment('Publishing Sonar assets...');

        $sourceDir = __DIR__.'/../../dist';
        $jsPath = public_path('vendor/laravel-sonar');

        // Create directory if it doesn't exist
        if (! File::exists($jsPath)) {
            File::makeDirectory($jsPath, 0755, true);
        }

        $sourcePath = $sourceDir.'/sonar.iife.js';
        $destinationPath = $jsPath.'/sonar.js';

        if (! File::exists($sourcePath)) {
            $this->error('Could not find sonar.iife.js. Please ensure you have built the assets.');
            $this->comment('Run these commands in the package directory:');
            $this->comment('npm install');
            $this->comment('npm run build');

            return self::FAILURE;
        }

        File::copy($sourcePath, $destinationPath);
        $this->info('Published: sonar.js');

        $this->info('Assets published successfully!');

        return self::SUCCESS;
    }
}
