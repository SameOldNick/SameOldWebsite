<?php

namespace App\Components\Captcha\Contracts;

use Illuminate\Contracts\View\View;

interface Presenter
{
    /**
     * Render the captcha.
     *
     * @param  array  $attributes  Attributes passed to the component.
     * @param  array  $data  The component data.
     * @return View|\Closure|string
     */
    public function render(array $attributes, array $data);
}
