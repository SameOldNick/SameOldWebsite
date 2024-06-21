<?php

namespace Tests\Feature\Traits;

trait DisablesVite
{
    public function setUpDisablesVite()
    {
        $this->withoutVite();
    }

    public function tearDownDisablesVite() {}
}
