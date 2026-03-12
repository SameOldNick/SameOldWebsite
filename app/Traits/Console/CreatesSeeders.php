<?php

namespace App\Traits\Console;

use Illuminate\Database\Seeder;

trait CreatesSeeders
{
    /**
     * Creates Seeder instance
     *
     * @param  class-string<Seeder>  $class
     * @return Seeder
     */
    protected function createSeeder(string $class)
    {
        return
            $this->getLaravel()->make($class)
                ->setContainer($this->getLaravel())
                ->setCommand($this);
    }
}
