<?php

namespace App\Components\Compiler\Compilers\Markdown\CommonMark\Extensions;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\Autolink\AutolinkExtension;
use League\CommonMark\Extension\CommonMark\Node;
use League\CommonMark\Extension\CommonMark\Renderer;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

class LinkExtension extends ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('link', Expect::structure([
            'autolink' => Expect::bool(true),
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addRenderer(Node\Inline\Link::class, new Renderer\Inline\LinkRenderer(), 0);

        if ($environment->getConfiguration()->get('link/autolink')) {
            $this->registerAutolink($environment);
        }
    }

    protected function registerAutolink(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addExtension(new AutolinkExtension);
    }
}
