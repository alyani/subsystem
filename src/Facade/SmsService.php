<?php

namespace Alyani\Subsystem\Facade;

use Illuminate\Support\Facades\Facade;
use Alyani\Subsystem\Services\SmsServiceManager;

/**
 * Class PortalService
 */
class SmsService extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return SmsServiceManager::class;
    }
}
