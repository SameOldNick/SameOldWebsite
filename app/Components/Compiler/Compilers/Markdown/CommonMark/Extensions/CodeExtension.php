<?php

namespace App\Components\Compiler\Compilers\Markdown\CommonMark\Extensions;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node;
use League\CommonMark\Extension\CommonMark\Renderer;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;
use Spatie\CommonMarkShikiHighlighter\HighlightCodeExtension;

class CodeExtension extends ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('code', Expect::structure([
            'theme' => Expect::string(),
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $theme = $environment->getConfiguration()->get('code/theme');

        $environment
            ->addRenderer(Node\Block\FencedCode::class, new Renderer\Block\FencedCodeRenderer(), 0)
            ->addRenderer(Node\Inline\Code::class, new Renderer\Inline\CodeRenderer(), 0)
            ->addRenderer(Node\Block\IndentedCode::class, new Renderer\Block\IndentedCodeRenderer(), 0)

            ->addExtension(new HighlightCodeExtension($theme));
    }
}
