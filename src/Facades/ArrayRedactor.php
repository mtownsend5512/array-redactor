<?php

namespace Mtownsend\ArrayRedactor\Facades;

use Illuminate\Support\Facades\Facade;

class ArrayRedactor extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'array_redactor';
    }
}
