<?php

use Schrojf\Papers\Http\Middleware\AuthorizePapers;

// config for Schrojf/Papers
return [

    'middleware' => [
        'web',
        AuthorizePapers::class,
    ],

    'api_middleware' => [
        'api',
        AuthorizePapers::class,
    ],

];
