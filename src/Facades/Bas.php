<?php

namespace ShikhBas\BasLaravelSdk\Facades;

use Illuminate\Support\Facades\Facade;

class Bas extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'bas'; // This is the alias we defined in the service provider
    }
}