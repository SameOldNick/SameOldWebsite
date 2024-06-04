<?php

namespace App\Console\Commands\Setup;

use App\Traits\Support\ExecutesCommandsExternally;
use Illuminate\Console\Command;

class SetupFrontend extends Command
{
    use ExecutesCommandsExternally;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:frontend
                            {--cmd-prefix= : Prefix to add to commands}
                            {--hide-output : Hide the output of the package manager commands}
                            {package-manager : The package manager to use (npm|yarn|pnpm)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install NodeJS packages and build front-end assets for production';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Parse command-line options
        $packageManager = $this->argument('package-manager');
        $hideOutput = $this->option('hide-output');

        // Disable process timeout
        $additionalArgs = ['timeout' => null];

        $outputs = ! $hideOutput ? [$this->getOutput()] : [];

        // Determine the correct command based on the package manager
        switch ($packageManager) {
            case 'npm':
                $npm = $this->prefixCommand('npm');

                $this->info('Installing NodeJS packages with npm...');
                $this->executeCommand($this->buildCommandLine("$npm install"), $outputs, $additionalArgs);
                $this->info('Building front-end assets for production...');
                $this->executeCommand($this->buildCommandLine("$npm run build"), $outputs, $additionalArgs);

                break;
            case 'yarn':
                $yarn = $this->prefixCommand('yarn');

                $this->info('Installing NodeJS packages with yarn...');
                $this->executeCommand($this->buildCommandLine("$yarn install"), $outputs, $additionalArgs);
                $this->info('Building front-end assets for production...');
                $this->executeCommand($this->buildCommandLine("$yarn run build"), $outputs, $additionalArgs);

                break;
            case 'pnpm':
                $pnpm = $this->prefixCommand('pnpm');

                $this->info('Installing NodeJS packages with pnpm...');
                $this->executeCommand($this->buildCommandLine("$pnpm install"), $outputs, $additionalArgs);
                $this->info('Building front-end assets for production...');
                $this->executeCommand($this->buildCommandLine("$pnpm run build"), $outputs, $additionalArgs);

                break;
            default:
                $this->error('Invalid choice. Exiting...');

                return 1;
        }

        $this->info('Compiled front-end assets.');
    }

    /**
     * Prefixes command (if needed)
     */
    protected function prefixCommand(string $command): string
    {
        // Check if the cmd-prefix option is set
        $cmdPrefix = $this->option('cmd-prefix');

        return $cmdPrefix ? "{$cmdPrefix} {$command}" : $command;
    }
}
