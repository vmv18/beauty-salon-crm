<?php

namespace App\Http\Controllers;

use OpenApi\Attributes as OA;

#[OA\Info(
    version: "1.0.0",
    title: "Beauty Salon CRM API",
    description: "API для системи управління салоном краси",
    contact: new OA\Contact(
        email: "info@beautysalon.com"
    ),
    license: new OA\License(
        name: "MIT"
    )
)]
#[OA\Server(
    url: "/api",
    description: "API Server"
)]
#[OA\SecurityScheme(
    securityScheme: "session",
    type: "apiKey",
    name: "X-CSRF-TOKEN",
    in: "header",
    description: "CSRF Token для автентифікації"
)]
abstract class Controller
{
    //
}
