<?php

require_once __DIR__ . '/../vendor/autoload.php';

if (!file_exists(__DIR__ . '/support/helpers.php')) {
    mkdir(__DIR__ . '/support');
    copy(__DIR__ . '/../vendor/workerman/webman-framework/src/support/helpers.php', __DIR__ . '/support/helpers.php');
}
require_once __DIR__ . '/support/helpers.php';

if (
    !file_exists(__DIR__ . '/config/plugin/webman-tech/laravel-validation')
    || !file_exists(__DIR__ . '/resource')
) {
    \WebmanTech\LaravelValidation\Install::install();
}

require_once __DIR__ . '/../vendor/workerman/webman-framework/src/support/bootstrap.php';
