<?php

if (! function_exists('once')) {
    /**
     * @template T
     *
     * @param (callable(): T) $callback
     * @return T
     */
    function once(callable $callback): mixed
    {
        if (function_exists('\Spatie\Once\once')) {
            return \Spatie\Once\once($callback);
        }

        return call_user_func($callback);
    }
}
