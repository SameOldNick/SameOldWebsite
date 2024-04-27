<?php

namespace App\Traits\Console;

trait CreatesSeeders
{
    /**
     * Creates Seeder instance
     *
     * @param class-string<\Illuminate\Database\Seeder> $class
     * @return \Illuminate\Database\Seeder
     */
    protected function createSeeder(string $class)
    {
        return
            $this->getLaravel()->make($class)
                ->setContainer($this->getLaravel())
                ->setCommand($this);
    }
}
