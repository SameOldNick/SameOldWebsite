<?php

namespace App\Components\Compiler\Compilers\Markdown\CommonMark;

use League\CommonMark\MarkdownConverter;
use League\CommonMark\Environment\Environment;
use League\CommonMark\Extension\CommonMark\CommonMarkCoreExtension;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;

class AllowableMarkdownConverter extends MarkdownConverter
{
    /**
     * Create a new Markdown converter pre-configured for GFM
     *
     * @param array<string, mixed> $config
     */
    public function __construct(array $config = [])
    {
        $environment = new Environment($config);
        $environment->addExtension(new CommonMarkCoreExtension());
        $environment->addExtension(new GithubFlavoredMarkdownExtension());

        parent::__construct($environment);
    }

    public function getEnvironment(): Environment
    {
        \assert($this->environment instanceof Environment);

        return $this->environment;
    }
}
