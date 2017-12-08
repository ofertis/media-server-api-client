<?php

namespace Ofertis\MediaServer;

use Illuminate\Support\Facades\Facade;

class MediaServerFacade extends Facade
{
    protected static function getFacadeAccessor() {
        return 'mediaserver';
    }
}
