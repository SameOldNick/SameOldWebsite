<?php

namespace App\Components\Security\Watchdogs;

use App\Components\Security\Exceptions\WatchdogException;
use App\Components\Security\Issues\ComposerAuditAbandonedAdvisory;
use App\Components\Security\Issues\ComposerAuditSecurityAdvisory;
use App\Components\Security\Enums\Severity;
use Composer\Console\Application;
use Illuminate\Support\Arr;
use Exception;
use Symfony\Component\Console\Input\ArrayInput;
use Symfony\Component\Console\Output\BufferedOutput;
use Illuminate\Support\Facades\Log;

final class ComposerAuditWatchdog implements WatchdogDriver
{
    private $application;

    public function __construct(
        protected readonly array $config
    )
    {
    }

    /**
     * Initializes the watchdog.
     *
     * @return void
     */
    public function initialize(): void
    {
        putenv('COMPOSER_HOME=' . base_path('/vendor/bin/composer'));

        $this->application = new Application();
        $this->application->setAutoExit(false);

        // Disable warning message
        $composer = $this->application->getComposer();

        if (Arr::get($composer->getConfig()->all(), 'config.audit.abandoned', 'default') === 'default') {
            $composer->getConfig()->merge([
                'config' => [
                    'audit' => [
                        'abandoned' => 'report',
                    ],
                ],
            ]);
        }
    }

    /**
     * Sniff for issues.
     *
     * @return array<\App\Components\Security\Issues\Issue>
     */
    public function sniff(): array
    {
        Log::info('Running the "composer audit --format=json" command.');

        $input = new ArrayInput(['command' => 'audit', '--format' => 'json']);
        $output = new BufferedOutput();

        $errorCode = $this->application->run($input, $output);

        if ($errorCode !== 0) {
            throw new WatchdogException('The "composer audit" command failed with error code %d.', $errorCode);
        }

        $contents = $output->fetch();

        /**
         * Example contents:
         * {
         *     "advisories": {
         *           "guzzlehttp/psr7": [
         *               {
         *                   "advisoryId": "PKSA-hn62-zkx4-1y5q",
         *                   "packageName": "guzzlehttp/psr7",
         *                   "affectedVersions": ">=2,<2.4.5|<1.9.1",
         *                   "title": "Improper header validation",
         *                   "cve": "CVE-2023-29197",
         *                   "link": "https://github.com/guzzle/psr7/security/advisories/GHSA-wxmh-65f7-jcvw",
         *                   "reportedAt": "2023-04-17T16:00:00+00:00",
         *                   "sources": [
         *                       {
         *                           "name": "GitHub",
         *                           "remoteId": "GHSA-wxmh-65f7-jcvw"
         *                      },
         *                       {
         *                           "name": "FriendsOfPHP/security-advisories",
         *                           "remoteId": "guzzlehttp/psr7/CVE-2023-29197.yaml"
         *                       }
         *                   ]
         *               }
         *           ]
         *       },
         *       "abandoned": {
         *           "fruitcake/laravel-cors": null,
         *           "swiftmailer/swiftmailer": "symfony/mailer"
         *       }
         *  }
         */

        try {
            $decoded = json_decode($contents, true, 100, JSON_THROW_ON_ERROR);

            if (!is_array($decoded)) {
                throw new Exception('JSON response is not an array.');
            }
        } catch (Exception $ex) {
            throw new WatchdogException('Unable to decode JSON from "composer audit" output', 0, $ex);
        }

        $issues = collect();

        if (Arr::get($this->config, 'advisories.enabled', false)) {
            $issues->push(...$this->mapAdvisoriesToIssues(Arr::get($decoded, 'advisories', [])));
        }

        if (Arr::get($this->config, 'abandoned.enabled', false)) {
            $issues->push(...$this->mapAbandonedToIssues(Arr::get($decoded, 'abandoned', [])));
        }

        return $issues->all();
    }

    /**
     * Cleans up with watchdog.
     *
     * @return void
     */
    public function cleanup(): void
    {
        $this->application = null;
    }

    protected function mapAdvisoriesToIssues(array $items)
    {
        $issues = [];

        if (empty($items)) {
            return $issues;
        }

        $severity = Severity::from(Arr::get($this->config, 'advisories.level'));

        foreach ($items as $package => $advisories) {
            $valid = array_filter($advisories, fn ($advisory) => Arr::has($advisory, ['advisoryId', 'packageName', 'title']));

            yield new ComposerAuditSecurityAdvisory($package, $valid, null, $severity);
        }
    }

    protected function mapAbandonedToIssues(array $items)
    {
        $severity = Severity::from(Arr::get($this->config, 'abandoned.level'));

        return Arr::map($items, fn ($replacement, $package) => new ComposerAuditAbandonedAdvisory($package, $replacement, null, $severity));
    }
}
