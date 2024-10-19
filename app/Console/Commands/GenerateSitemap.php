<?php

namespace App\Console\Commands;

use App\Models\Article;
use Illuminate\Console\Command;
use Spatie\Sitemap\Sitemap;
use Spatie\Sitemap\SitemapGenerator;
use Spatie\Sitemap\SitemapIndex;

class GenerateSitemap extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sitemap:generate';

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

        $sitemap = Sitemap::create();

        foreach ($this->getSitemaps() as $value) {
            $sitemap->add($value->getTags());
        }

        $sitemap->writeToFile(public_path('sitemap.xml'));

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
     *
     * @return Sitemap
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
     *
     * @return Sitemap
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
     *
     * @return Sitemap
     */
    protected function getArticlesSitemap(): Sitemap
    {
        $articles = Article::published()->get();

        return Sitemap::create()->add($articles);
    }
}
