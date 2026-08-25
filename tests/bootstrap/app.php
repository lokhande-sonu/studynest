<?php
/**
 * START: imohitmehto | 2026-08-25 | Created: Test bootstrap for automated test suite
 * Provides application bootstrap for PHPUnit test runner.
 * END: imohitmehto | Created: Test bootstrap
 */

use Illuminate\Contracts\Console\Kernel;

define('LARAVEL_START', microtime(true));

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
*/

require __DIR__.'/../../vendor/autoload.php';

/*
|--------------------------------------------------------------------------
| Run The Artisan Application
|--------------------------------------------------------------------------
*/

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);

$status = $kernel->handle(
    $input = new Symfony\Component\Console\Input\ArgvInput,
    new Symfony\Component\Console\Output\ConsoleOutput
);

/*
|--------------------------------------------------------------------------
| Shutdown The Application
|--------------------------------------------------------------------------
*/

$kernel->terminate($input, $status);
