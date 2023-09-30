<?php

namespace App\Components\Placeholders;

use RuntimeException;
use Illuminate\Container\Container;
use Illuminate\Support\Str;

class Options
{
    protected $builders;
    protected $defaultBuilders;
    protected $customBuilder;

    /**
     * Initializes placeholder options
     *
     * @param Container $container
     */
    public function __construct(
        protected Container $container
    )
    {
        $this->builders = [];
        $this->defaultBuilders = false;
        $this->customBuilder = new Builders\CustomBuilder();
    }

    /**
     * Uses builder callback
     *
     * @param callable $builder
     * @return $this
     */
    public function useBuilder(callable $builder) {
        $this->builders = [...$this->builders, $builder];

        return $this;
    }

    /**
     * Uses default builders
     *
     * @param boolean $enabled If true, uses default builders. (default: true)
     * @return $this
     */
    public function useDefaultBuilders(bool $enabled = true) {
        $this->defaultBuilders = $enabled;

        return $this;
    }

    /**
     * Checks if custom placeholder exists
     *
     * @param string $placeholder
     * @return boolean
     */
    public function has(string $placeholder) {
        return $this->customBuilder->has($placeholder);
    }

    /**
     * Sets custom placeholder
     *
     * @param string $placeholder
     * @param string|callable $value
     * @return $this
     */
    public function set(string $placeholder, $value) {
        $value = !is_callable($value) ? fn () => $value : $value;

        $this->customBuilder->set($placeholder, $value);

        return $this;
    }

    /**
     * Removes placeholder
     *
     * @param string $placeholder
     * @return $this
     */
    public function remove(string $placeholder) {
        $this->customBuilder->remove($placeholder);

        return $this;
    }

    /**
     * Gets the builders
     *
     * @return array
     */
    public function getBuilders() {
        if ($this->defaultBuilders)
            return [...$this->getDefaultBuilders(), $this->customBuilder, ...$this->builders];
        else
            return [$this->customBuilder, ...$this->builders];
    }

    /**
     * Gets the default builders
     *
     * @return array
     */
    protected function getDefaultBuilders() {
        return [
            $this->container->make(Builders\GeneralBuilder::class),
            $this->container->make(Builders\RequestBuilder::class),
        ];
    }

    /**
     * Checks if placeholder is set.
     *
     * @param string $name
     * @return boolean
     */
    public function __isset($name)
    {
        return $this->has($name);
    }

    /**
     * Sets placeholder
     *
     * @param string $name Placeholder (this is transformed to kebab-case)
     * @param string|callable $value
     */
    public function __set($name, $value)
    {
        $placeholder = Str::kebab($name);

        $this->set($placeholder, $value);
    }

    /**
     * Sets placeholder
     *
     * @param string $method Placeholder (this is transformed to kebab-case)
     * @param array $params
     * @return $this
     */
    public function __call(string $method, array $params) {
        if (count($params) === 0)
            throw new RuntimeException('No value specified for placeholder "' + $method + '" .');

        $placeholder = Str::kebab($method);

        return $this->set($placeholder, $params[0]);
    }
}
