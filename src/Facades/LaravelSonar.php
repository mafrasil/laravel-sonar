<?php

namespace Mafrasil\LaravelSonar\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Mafrasil\LaravelSonar\LaravelSonar
 */
class LaravelSonar extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Mafrasil\LaravelSonar\LaravelSonar::class;
    }
}
