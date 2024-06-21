<?php

namespace App\Components\Dusk\Console;

use Illuminate\Support\Str;
use Laravel\Dusk\Console\DuskCommand as BaseDuskCommand;
use RuntimeException;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Process\Process;
use Throwable;

class DuskCommand extends BaseDuskCommand
{
    /**
     * Serve process
     *
     * @var ?Process
     */
    protected $serveProcess;

    /**
     * {@inheritDoc}
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * {@inheritDoc}
     */
    protected function configure()
    {
        $this->getDefinition()->addOptions([
            new InputOption('serve', description: 'Spin up web app using PHP server'),
            new InputOption('serve-host', mode: InputOption::VALUE_OPTIONAL, default: '127.0.0.1', description: 'IP address to bind web server to'),
            new InputOption('serve-port', mode: InputOption::VALUE_OPTIONAL, default: '8888', description: 'Port to bind web server to'),
            new InputOption('serve-url', mode: InputOption::VALUE_OPTIONAL, description: 'The URL of the web server. If not specified, it is generated automatically.'),
        ]);
    }

    /**
     * {@inheritDoc}
     */
    protected function setupDuskEnvironment()
    {
        parent::setupDuskEnvironment();

        if ($this->option('serve')) {
            try {
                $this->serve();
            } catch (Throwable $ex) {
                // The teardown needs to occur so the previous configuration is restored.
                $this->teardownDuskEnviroment();

                exit(1);
            }
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function teardownDuskEnviroment()
    {
        parent::teardownDuskEnviroment();

        if (isset($this->serveProcess)) {
            $this->info('Stopping web server...');
            $this->serveProcess->stop();
        }
    }

    /**
     * Serve the web app
     *
     * @return void
     *
     * @throws RuntimeException Thrown if web server can't be started.
     */
    protected function serve()
    {
        $this->info('Starting web server...');

        $this->serveProcess = $this->buildServeProcess();

        $this->serveProcess->start(function ($type, $data) {
            $this->output->write($data);
        });

        // Make sure process is running
        if (! $this->serveProcess->waitUntil(fn () => $this->serveProcess->isRunning())) {
            $this->error('An error occurred starting the web server:');
            $this->error($this->serveProcess->getOutput());

            throw new RuntimeException('Unable to start web server.');
        }
    }

    /**
     * Build the process to run the web server
     *
     * @return \Symfony\Component\Process\Process
     */
    protected function buildServeProcess()
    {
        $host = $this->option('serve-host');
        $port = (int) $this->option('serve-port');

        $command = sprintf('%s artisan serve --host=%s --port=%d', PHP_BINARY, $host, $port);

        return Process::fromShellCommandline($command, base_path(), $this->getServeEnvVariables());
    }

    /**
     * Gets environment variables to include with web server
     */
    protected function getServeEnvVariables(): array
    {
        return [
            'APP_URL' => $this->getServeUrl(),
        ];
    }

    /**
     * Gets the URL to the web server
     */
    protected function getServeUrl(): string
    {
        if ($this->option('serve-url')) {
            return $this->option('serve-url');
        } else {
            $host = $this->option('serve-host') !== '0.0.0.0' ? $this->option('serve-host') : '127.0.0.1';
            $port = (int) $this->option('serve-port');

            return sprintf('http://%s:%d', $host, $port);
        }
    }

    /**
     * {@inheritDoc}
     */
    protected function phpunitArguments($options)
    {
        // Remove --serve options
        $options = array_values(array_filter($options, function ($option) {
            return ! Str::startsWith($option, '--serve');
        }));

        return parent::phpunitArguments($options);
    }

    /**
     * {@inheritDoc}
     */
    protected function env()
    {
        $variables = [];

        if ($this->option('serve')) {
            $variables['APP_URL'] = $this->getServeUrl();
        }

        return array_merge(parent::env(), $variables);
    }
}
