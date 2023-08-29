<?php

namespace App\Components\Compiler\Compilers\Markdown\CommonMark\Extensions;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node;
use League\CommonMark\Extension\CommonMark\Parser;
use League\CommonMark\Extension\CommonMark\Renderer;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

class HeadingExtension extends ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('heading', Expect::structure([
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addBlockStartParser(new Parser\Block\HeadingStartParser(), 60)
            ->addRenderer(Node\Block\Heading::class, new Renderer\Block\HeadingRenderer(), 0);
    }
}
