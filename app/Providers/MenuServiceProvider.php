<?php

namespace App\Providers;

use App\Components\Menus\Facades\Menus;
use App\Components\Menus\Items\DropdownItem;
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
        if ($this->app->runningInConsole() && ! $this->app->runningUnitTests()) {
            return;
        }

        Menus::add('main', function (Menu $menu) {
            $this->createSharedMenu($menu);
        });

        Menus::add('footer', function (Menu $menu) {
            $this->createSharedMenu($menu)
                ->dropdown('Legal', function (DropdownItem $dropdown) {
                    $dropdown
                        ->icon('fas-scale-balanced')
                        ->route('terms-conditions', 'Terms & Conditions')
                        ->route('privacy-policy', 'Privacy Policy');
                });
        });

        Menus::add('blog.sidebar.archives', function (Menu $menu) {
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
     * Creates shared menu for main and fotter.
     *
     * @return Menu
     */
    private function createSharedMenu(Menu $menu)
    {
        return $menu
            ->route('home', 'Home', $this->applyIcon('fas-house'))
            ->route('blog', 'Blog', $this->applyIcon('fas-newspaper'))
            ->route('contact', 'Contact Me', $this->applyIcon('fas-envelope'));
    }

    /**
     * Applies icon to link item
     *
     * @param  string  $icon  Icon class
     * @return callable Function that sets icon prop when called.
     */
    private function applyIcon($icon)
    {
        return function (LinkItem $item) use ($icon) {
            $item->icon($icon);
        };
    }
}
