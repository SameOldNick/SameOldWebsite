<?php

namespace App\Components\Compiler;

use App\Components\Compiler\Compilers\Markdown\MarkdownCompiler;
use Illuminate\Support\Manager;

class CompilerManager extends Manager
{
    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return 'markdown';
    }

    protected function createMarkdownDriver()
    {
        return new MarkdownCompiler;
    }
}
