<?php

namespace App\Components\Compiler\Compilers\Markdown\CommonMark\Extensions;

use League\CommonMark\Environment\EnvironmentBuilderInterface;
use League\CommonMark\Extension\CommonMark\Node;
use League\CommonMark\Extension\CommonMark\Parser;
use League\CommonMark\Extension\CommonMark\Renderer;
use League\CommonMark\Extension\ConfigurableExtensionInterface;
use League\CommonMark\Extension\DisallowedRawHtml\DisallowedRawHtmlExtension;
use League\CommonMark\Node as CoreNode;
use League\CommonMark\Parser as CoreParser;
use League\CommonMark\Renderer as CoreRenderer;
use League\Config\ConfigurationBuilderInterface;
use Nette\Schema\Expect;

class CommonExtension implements ConfigurableExtensionInterface
{
    public function configureSchema(ConfigurationBuilderInterface $builder): void
    {
        $builder->addSchema('common', Expect::structure([
        ]));
    }

    public function register(EnvironmentBuilderInterface $environment): void
    {
        $environment
            ->addExtension(new DisallowedRawHtmlExtension)

            ->addInlineParser(new CoreParser\Inline\NewlineParser, 200)
            ->addInlineParser(new Parser\Inline\CloseBracketParser, 30)
            ->addInlineParser(new Parser\Inline\OpenBracketParser, 20)
            ->addInlineParser(new Parser\Inline\BangParser, 10)
            ->addInlineParser(new Parser\Inline\BacktickParser, 150)
            ->addInlineParser(new Parser\Inline\AutolinkParser, 50)

            ->addBlockStartParser(new Parser\Block\FencedCodeStartParser, 50)
            ->addBlockStartParser(new Parser\Block\BlockQuoteStartParser, 70)
            ->addBlockStartParser(new Parser\Block\HtmlBlockStartParser, 40)

            ->addInlineParser(new CoreParser\Inline\NewlineParser, 200)
            ->addInlineParser(new Parser\Inline\EscapableParser, 80)
            ->addInlineParser(new Parser\Inline\EntityParser, 70)
            ->addInlineParser(new Parser\Inline\HtmlInlineParser, 40)

            ->addRenderer(CoreNode\Block\Document::class, new CoreRenderer\Block\DocumentRenderer, 0)
            ->addRenderer(Node\Block\HtmlBlock::class, new Renderer\Block\HtmlBlockRenderer, 0)
            ->addRenderer(Node\Inline\HtmlInline::class, new Renderer\Inline\HtmlInlineRenderer, 0)
            ->addRenderer(CoreNode\Inline\Newline::class, new CoreRenderer\Inline\NewlineRenderer, 0);
    }
}
