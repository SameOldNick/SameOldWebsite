<?php

namespace App\Components\Compiler\Compilers\Markdown\CommonMark\Extensions;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Delimiter\Processor\EmphasisDelimiterProcessor;
use League\CommonMark\Extension\CommonMark\Node;
use League\CommonMark\Extension\CommonMark\Renderer;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\CommonMark\Extension\Strikethrough\StrikethroughExtension;
use League\CommonMark\Node as CoreNode;
use League\CommonMark\Renderer as CoreRenderer;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

class TypographyExtension implements ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('typography', Expect::structure([
            'bold' => Expect::bool(true),
            'italic' => Expect::bool(true),
            'strikethrough' => Expect::bool(true),
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addRenderer(CoreNode\Block\Paragraph::class, new CoreRenderer\Block\ParagraphRenderer, 0)
            ->addRenderer(CoreNode\Inline\Text::class, new CoreRenderer\Inline\TextRenderer, 0);

        if ($environment->getConfiguration()->get('typography/bold')) {
            $this->registerBold($environment);
        }

        if ($environment->getConfiguration()->get('typography/italic')) {
            $this->registerItalic($environment);
        }

        if ($environment->getConfiguration()->get('typography/strikethrough')) {
            $this->registerStrikethrough($environment);
        }
    }

    protected function registerBold(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addRenderer(Node\Inline\Strong::class, new Renderer\Inline\StrongRenderer, 0);
    }

    protected function registerItalic(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addRenderer(Node\Inline\Emphasis::class, new Renderer\Inline\EmphasisRenderer, 0);

        if ($environment->getConfiguration()->get('commonmark/use_asterisk')) {
            $environment->addDelimiterProcessor(new EmphasisDelimiterProcessor('*'));
        }

        if ($environment->getConfiguration()->get('commonmark/use_underscore')) {
            $environment->addDelimiterProcessor(new EmphasisDelimiterProcessor('_'));
        }
    }

    protected function registerStrikethrough(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addExtension(new StrikethroughExtension);
    }
}
