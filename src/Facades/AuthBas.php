<?php

namespace ShikhBas\BasLaravelSdk\Facades;

use Illuminate\Support\Facades\Facade;

class AuthBas extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'authbas'; // This is the alias we defined in the service provider
    }
}