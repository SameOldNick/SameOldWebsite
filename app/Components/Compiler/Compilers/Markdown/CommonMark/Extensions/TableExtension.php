<?php

namespace App\Components\Compiler\Compilers\Markdown\CommonMark\Extensions;

use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use League\CommonMark\Environment\EnvironmentBuilderInterface;
use Nette\Schema\Expect;

use League\CommonMark\Extension\Table\TableExtension as CommonMarkTableExtension;

class TableExtension extends ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('table', Expect::structure([
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addExtension(new CommonMarkTableExtension());
    }
}
