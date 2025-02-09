<?php

namespace ShikhBas\BasLaravelSdk\Facades;

use Illuminate\Support\Facades\Facade;

class PaymentBas extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'paymentbas'; // This is the alias we defined in the service provider
    }
}