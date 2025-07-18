<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Authentication Defaults
    |--------------------------------------------------------------------------
    |
    | Define el guard y broker de contraseñas por defecto para la aplicación.
    |
    */

    'defaults' => [
        'guard' => env('AUTH_GUARD', 'web'),
        'passwords' => env('AUTH_PASSWORD_BROKER', 'profesores'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Authentication Guards
    |--------------------------------------------------------------------------
    |
    | Aquí defines todos los guards de autenticación de tu app.
    |
    | Cada guard utiliza un provider que define cómo obtener los usuarios.
    |
    | Soportados: "session"
    |
    */

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'profesores',
        ],

        // ✅ Guard para profesores
        'profesor' => [
            'driver' => 'session',
            'provider' => 'profesores',
        ],

        // ✅ Guard para alumnos
        'alumno' => [
            'driver' => 'session',
            'provider' => 'alumnos',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | User Providers
    |--------------------------------------------------------------------------
    |
    | Aquí defines cómo se obtienen los usuarios desde la base de datos.
    |
    | Soportados: "eloquent", "database"
    |
    */

    'providers' => [
        // ✅ Provider para profesores
        'profesores' => [
            'driver' => 'eloquent',
            'model' => App\Models\Profesor::class,
        ],

        // ✅ Provider para alumnos
        'alumnos' => [
            'driver' => 'eloquent',
            'model' => App\Models\Alumno::class,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Resetting Passwords
    |--------------------------------------------------------------------------
    |
    | Configura el reseteo de contraseñas para cada tipo de usuario.
    |
    */

    'passwords' => [
        'profesores' => [
            'provider' => 'profesores',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],

        'alumnos' => [
            'provider' => 'alumnos',
            'table' => env('AUTH_PASSWORD_RESET_TOKEN_TABLE', 'password_reset_tokens'),
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Password Confirmation Timeout
    |--------------------------------------------------------------------------
    |
    | Tiempo en segundos antes de que expire la confirmación de contraseña.
    |
    */

    'password_timeout' => env('AUTH_PASSWORD_TIMEOUT', 10800),

];
