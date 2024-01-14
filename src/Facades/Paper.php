<?php

namespace Schrojf\Paper\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Schrojf\Paper\Paper
 */
class Paper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Schrojf\Paper\Paper::class;
    }
}
