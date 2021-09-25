<?php
/*
 *
 * (c) Terry L. <contact@terryl.in>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

/**
 * Register to PSR-4 autoloader.
 *
 * @return void
 */
function sinopac_php_sdk_register()
{
    spl_autoload_register('sinopac_php_sdk_autoload', true, false);
}

/**
 * PSR-4 autoloader.
 *
 * @param string $className
 * 
 * @return void
 */
function sinopac_php_sdk_autoload($className)
{
    $prefix = 'Sinopac\\';
    $dir = __DIR__ . '/src';

    if (0 === strpos($className, $prefix . 'Psr')) {
        $parts = explode('\\', substr($className, strlen($prefix)));
        $filepath = $dir . '/' . implode('/', $parts) . '.php';

        if (is_file($filepath)) {
            require $filepath;
        }
    }
}

sinopac_php_sdk_register();