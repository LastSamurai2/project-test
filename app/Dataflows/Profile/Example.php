<?php
/**
 * Copyright © Alekseon sp. z o.o.
 * http://www.alekseon.com/
 */
namespace App\Dataflows\Profile;

class Example extends \App\Models\Dataflows\Profile
{
    /**
     *
     */
    public function execute()
    {
        $this->addInfoLog('ok');
    }
}
