<?php

namespace App\Components\Compiler\Compilers\Markdown\CommonMark\Extensions;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node;
use League\CommonMark\Extension\CommonMark\Renderer;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

class ImageExtension implements ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('image', Expect::structure([
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addRenderer(Node\Inline\Image::class, new Renderer\Inline\ImageRenderer, 0);
    }
}
