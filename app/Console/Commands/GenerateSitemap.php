<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate {--force : Force sitemap to be created} {output : File to write sitemap to}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate the sitemap.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $outputFile = $this->argument('output');

        if (! $this->option('force') && ! is_writable($outputFile)) {
            $this->error(sprintf('The file "%s" is not writable.', $outputFile));

            return 1;
        }

        $this->info('Generating sitemap...');

        $sitemap = Sitemap::create();

        foreach ($this->getSitemaps() as $value) {
            $sitemap->add($value->getTags());
        }

        if (count($sitemap->getTags()) === 0) {
            $this->info('No tags were to found to add to sitemap.');

            if (! $this->option('force') && ! $this->confirm('Would you like to continue creating the sitemap?')) {
                $this->info('Exiting...');

                return 1;
            }
        }

        $sitemap->writeToFile($outputFile);

        $this->info(sprintf('Sitemap saved to "%s".', $outputFile));
    }

    /**
     * Gets sitemaps to include.
     *
     * @return Sitemap[]
     */
    protected function getSitemaps(): array
    {
        return [
            $this->getPagesSitemap(),
            $this->getAuthSitemap(),
            $this->getArticlesSitemap(),
        ];
    }

    /**
     * Gets pages sitemap
     */
    protected function getPagesSitemap(): Sitemap
    {
        return Sitemap::create()->add([
            route('home'),
            route('blog'),
            route('contact'),
        ]);
    }

    /**
     * Gets auth sitemap
     */
    protected function getAuthSitemap(): Sitemap
    {
        return Sitemap::create()->add([
            route('login'),
            route('register'),
            route('password.email'),
        ]);
    }

    /**
     * Gets articles sitemap
     */
    protected function getArticlesSitemap(): Sitemap
    {
        $articles = Article::published()->get();

        return Sitemap::create()->add($articles);
    }
}
