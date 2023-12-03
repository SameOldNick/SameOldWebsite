<?php

namespace Tests\Feature\Traits;

use Database\Seeders\Setup;
use Database\Seeders\Test\SlimCountryStateSeeder;

trait DisablesVite
{
    public function setUpDisablesVite()
    {
        $this->withoutVite();
    }


    public function tearDownDisablesVite()
    {

    }
}
