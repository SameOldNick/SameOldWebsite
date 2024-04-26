<?php

namespace App\Traits\Console;

use App\Console\Helpers\StackOutput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Process\Exception\ProcessFailedException;
use Symfony\Component\Process\Process;

trait ExecutesCommandsExternally
{
    /**
     * Executes a command unsafely (no sanitization on the command is performed)
     *
     * @param string $commandLine
     * @param bool $realTimeOutput If true, process output is immediately sent to the terminal.
     * @param array $additional Additional arguments to use when creating Process. (default: empty array)
     * @return string Returns command output
     */
    protected function executeCommand($commandLine, bool $realTimeOutput = false, array $additional = [])
    {
        // Create output stack
        $bufferedOutput = new BufferedOutput();

        $stackOutput = new StackOutput($realTimeOutput ? [$bufferedOutput, $this->getOutput()] : [$bufferedOutput]);

        // Create process from command line and run it
        $process = $this->runProcess(
            Process::fromShellCommandline($commandLine, ...$additional),
            $stackOutput
        );

        // Executes after the command finishes
        if (! $process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        return $bufferedOutput->fetch();
    }

    /**
     * Runs process
     *
     * @param Process $process
     * @param OutputInterface $stdout Stream for output
     * @param OutputInterface|null $stderr Error stream (output stream is used if null)
     * @return Process
     */
    protected function runProcess(Process $process, OutputInterface $stdout, ?OutputInterface $stderr = null)
    {
        // Set $stderr to $stdout if $stderr is not specified.
        if (is_null($stderr)) {
            $stderr = $stdout;
        }

        // Run process and send output to appropriate interfaces
        $process->run(function ($type, $buffer) use ($stderr, $stdout): void {
            if (Process::ERR === $type) {
                $stderr->write($buffer);
            } else {
                $stdout->write($buffer);
            }
        });

        return $process;
    }

    /**
     * Safely builds the command line
     *
     * @param string $command Command
     * @param string ...$args Arguments
     * @return string Escaped command line
     */
    protected function buildCommandLine($command, ...$args)
    {
        $command = escapeshellcmd($command);

        return count($args) > 0 ? sprintf('%s %s', $command, implode(' ', array_map(fn ($arg) => escapeshellarg($arg), $args))) : $command;
    }
}
