<?php

namespace Tests\Feature;

use Tests\TestCase;

use App\Components\Placeholders\Factory;
use App\Components\Placeholders\Options;
use App\Components\Placeholders\PlaceholderCollection;
use App\Components\Placeholders\TagCompiler;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Contracts\Http\Kernel;

class PlaceholdersTest extends TestCase
{
    use WithFaker;

    /**
     * Tests a placeholder collection is built with nothing.
     */
    public function test_placeholder_collection_built(): void
    {
        $factory = app(Factory::class);

        $collection = $factory->build(function (Options $options) {

        });

        $this->assertInstanceOf(PlaceholderCollection::class, $collection);
        $this->assertEmpty($collection);
    }

    /**
     * Tests a placeholder collection is built with defaults.
     */
    public function test_placeholder_collection_built_defaults(): void
    {
        $factory = app(Factory::class);

        $collection = $factory->build(function (Options $options) {
            $options
                ->useDefaultBuilders();
        });

        $this->assertInstanceOf(PlaceholderCollection::class, $collection);
        $this->assertNotEmpty($collection);
    }

    /**
     * Tests a placeholder collection is built with defaults and custom builder.
     */
    public function test_placeholder_collection_built_defaults_custom(): void
    {
        $factory = app(Factory::class);

        $key = $this->faker->slug;
        $value = $this->faker->uuid;

        $collection = $factory->build(function (Options $options) use ($key, $value) {
            $options
                ->useDefaultBuilders()
                ->$key($value);
        });

        $this->assertInstanceOf(PlaceholderCollection::class, $collection);
        $this->assertNotEmpty($collection);
        $this->assertTrue($collection->has($key));
        $this->assertEquals($value, $collection->value($key));
    }

    /**
     * Tests a placeholder collection is built with a placeholder overriding an existing placeholder.
     */
    public function test_placeholder_collection_built_override(): void
    {
        $factory = app(Factory::class);

        $value = $this->faker->uuid;

        $collection = $factory->build(function (Options $options) use ($value) {
            $options
                ->useDefaultBuilders()
                ->set('ip-address', $value);
        });

        $this->assertInstanceOf(PlaceholderCollection::class, $collection);
        $this->assertNotEmpty($collection);
        $this->assertTrue($collection->has('ip-address'));
        $this->assertEquals($value, $collection->value('ip-address'));
    }

    /**
     * Tests placeholder tags are compiled.
     */
    public function test_placeholder_tags_compiled(): void
    {
        $factory = app(Factory::class);

        $key = $this->faker->slug;
        $value = $this->faker->uuid;

        $collection = $factory->build(function (Options $options) use ($key, $value) {
            $options
                ->useDefaultBuilders()
                ->$key($value);
        });

        $compiler = new TagCompiler($collection);

        $template = sprintf('[%s] [datetime] [ip]', $key);

        $compiled = $compiler->compile($template);

        $this->assertNotEquals($template, $compiled);
        $this->assertTrue(str_contains($compiled, $value));
        $this->assertTrue(str_contains($compiled, app(Kernel::class)->requestStartedAt()));
        $this->assertTrue(str_contains($compiled, request()->ip()));
        $this->assertEquals(0, preg_match('/[[^\]+]]/', $compiled));
    }

    /**
     * Tests placeholder tags are only compiled once.
     */
    public function test_placeholder_tags_compiled_once(): void
    {
        $factory = app(Factory::class);

        $key = $this->faker->slug;
        $value = sprintf('%s [ip]', $this->faker->uuid);

        $collection = $factory->build(function (Options $options) use ($key, $value) {
            $options
                ->useDefaultBuilders()
                ->$key($value);
        });

        $compiler = new TagCompiler($collection);

        $template = sprintf('[%s]', $key);

        $compiled = $compiler->compile($template);

        $this->assertNotEquals($template, $compiled);
        $this->assertTrue(str_contains($compiled, $value));
        $this->assertFalse(str_contains($compiled, request()->ip()));
        $this->assertEquals(0, preg_match('/[[^\]+]]/', $compiled));
    }
}
