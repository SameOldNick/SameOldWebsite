<?php

namespace App\Components\Compiler\Compilers;

interface Compiler {
    public function compile(string $input, array $config = []);
}
