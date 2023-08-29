<?php

namespace App\Providers;

use App\Components\Menus\Facades\Menus;
use App\Components\Menus\Items\LinkItem;
use App\Components\Menus\Menu;
use App\Models\Article;
use Illuminate\Support\Carbon;
use Illuminate\Support\ServiceProvider;

class MenuServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole() || $this->app->runningUnitTests()) {
            return;
        }

        Menus::create('main.top', function (Menu $menu) {
            $menu
                ->route('home', 'Home', $this->applyIcon('fa-solid fa-house'))
                ->route('blog', 'Blog', $this->applyIcon('fa-solid fa-newspaper'))
                ->url('/contact', 'Contact Me', $this->applyIcon('fa-solid fa-envelope'));
        });

        Menus::create('blog.sidebar.archives', function (Menu $menu) {
            $yearMonths = Article::published()
                ->get()
                    ->groupedByDateTime('Y-m')
                    ->sortKeysDesc()
                    ->keys()
                        ->map(fn ($value) => Carbon::parse($value));

            foreach ($yearMonths as $yearMonth) {
                $menu->route(['blog.archive', ['year' => $yearMonth->year, 'month' => $yearMonth->month]], $yearMonth->format('F Y'));
            }
        });
    }

    /**
     * Applies icon to link item
     *
     * @param string $icon Icon class
     * @return callable Function that sets icon prop when called.
     */
    private function applyIcon($icon) {
        return function (LinkItem $item) use ($icon) {
            $item->icon($icon);
        };
    }
}
