<?php

namespace App\Components\Captcha\Contracts;

interface Presenter
{
    /**
     * Render the captcha.
     *
     * @param  array  $attributes  Attributes passed to the component.
     * @param  array  $data  The component data.
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render(array $attributes, array $data);
}
