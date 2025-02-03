<?php

namespace Mafrasil\LaravelSonar\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class ExportReactCommand extends Command
{
    public $signature = 'sonar:export-react {directory? : The directory to export the React components to}';

    public $description = 'Export React components for use in your application';

    /**
     * @var array<string>
     */
    protected array $reactComponents = [
        'components/SonarTracker.tsx',
        'hooks/useSonar.ts',
        'core.ts',
        'types.ts',
    ];

    protected $example = [
        'import { SonarTracker } from "./Sonar/Components/SonarTracker";',
        'import { useSonar } from "./Sonar/Hooks/useSonar";',
        '',
        'function MyComponent() {',
        '    const { track } = useSonar();',
        '    return (',
        '        <SonarTracker name="my-component">',
        '            <button>Track Me</button>',
        '        </SonarTracker>',
        '    );',
        '}',
    ];

    public function handle(): int
    {
        $targetDir = $this->argument('directory') ?? resource_path('js/Sonar');
        $packagePath = realpath(dirname(dirname(dirname(__FILE__))));
        $resourcesPath = $packagePath.'/resources/js';

        $copiedFiles = 0;

        foreach ($this->reactComponents as $file) {
            $source = $resourcesPath.'/'.$file;
            $destination = $targetDir.'/'.$file;

            if (! File::exists($source) || ! is_readable($source)) {
                $this->error("Could not read source file: {$file}");

                continue;
            }

            $dirPath = dirname($destination);
            if (! File::exists($dirPath)) {
                File::makeDirectory($dirPath, 0755, true);
            }

            try {
                $contents = File::get($source);
                File::put($destination, $contents);
                $this->info("✓ Exported: {$file}");
                $copiedFiles++;
            } catch (\Exception $e) {
                $this->error("Error processing {$file}: ".$e->getMessage());
            }
        }

        if ($copiedFiles === 0) {
            $this->error("\nNo files were copied!");

            return self::FAILURE;
        }

        $this->newLine();
        $this->info('Usage Example:');
        $this->newLine();
        foreach ($this->example as $line) {
            $this->line($line);
        }

        return self::SUCCESS;
    }
}
