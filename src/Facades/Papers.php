<?php

namespace Schrojf\Papers\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Schrojf\Papers\Papers
 */
class Papers extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Schrojf\Papers\Papers::class;
    }
}
