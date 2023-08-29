<?php

namespace App\Components\Compiler\Compilers\Markdown\CommonMark\Extensions;

use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Renderer;
use League\CommonMark\Extension\CommonMark\Node;
use Nette\Schema\Expect;


class QuoteExtension extends ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('quote', Expect::structure([
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addRenderer(Node\Block\BlockQuote::class,    new Renderer\Block\BlockQuoteRenderer(),    0);
    }
}
