<?php

namespace Bagoesz21\LaravelNotifWaWeb\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelNotifWaWeb extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-notif-wa-web';
    }
}
