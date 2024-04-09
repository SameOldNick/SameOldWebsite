<?php

namespace App\Components\Compiler\Compilers\Markdown\CommonMark\Extensions;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node;
use League\CommonMark\Extension\CommonMark\Parser;
use League\CommonMark\Extension\CommonMark\Renderer;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\CommonMark\Extension\TaskList\TaskListExtension;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

class ListExtension implements ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('list', Expect::structure([
            'tasklist' => Expect::bool(true),
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addBlockStartParser(new Parser\Block\ListBlockStartParser(), 10)
            ->addRenderer(Node\Block\ListBlock::class, new Renderer\Block\ListBlockRenderer(), 0)
            ->addRenderer(Node\Block\ListItem::class, new Renderer\Block\ListItemRenderer(), 0);

        if ($environment->getConfiguration()->get('list/tasklist')) {
            $this->registerTasklist($environment);
        }
    }

    protected function registerTasklist(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addExtension(new TaskListExtension);
    }
}
