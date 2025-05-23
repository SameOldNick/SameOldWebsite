<?php

namespace App\Components\Captcha\Components;

use App\Components\Captcha\CaptchaManager;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Illuminate\View\Component;

class CaptchaComponent extends Component
{
    /**
     * Create a new component instance.
     *
     * @return void
     */
    public function __construct(
        public readonly CaptchaManager $manager,
        public readonly ?string $driver = null,

    ) {
        //
    }

    /**
     * Get the view / contents that represent the component.
     *
     * @return \Illuminate\Contracts\View\View|\Closure|string
     */
    public function render()
    {
        return function (array $data) {
            if (! $this->manager->driver($this->driver)->isReady()) {
                return '';
            }

            // Ensure all attribute keys are camelCase
            $attributes = Arr::mapWithKeys(
                $this->attributes->all(),
                fn ($value, $key) => [Str::camel($key) => $value]
            );

            return $this->manager->driver($this->driver)->presenter()->render($attributes, $data);
        };
    }
}
