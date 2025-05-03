<?php

namespace Schrojf\Papers\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Schrojf\Papers\Paper
 */
class Paper extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Schrojf\Papers\Paper::class;
    }
}
