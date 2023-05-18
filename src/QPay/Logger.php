<?php
/*
 * This file is part of the Sinopac PHP SDK package.
 *
 * (c) Terry L. <contact@terryl.in>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sinopac\QPay;

use Sinopac\Exception\QPayException;

/**
 * The Logger trait
 *
 * @deprecated 0.2.1
 */
trait Logger
{
    /**
     * The logger instance.
     *
     * @var \Monolog\Logger
     */
    private $logger = null;

    /**
     * Set a logger.
     *
     * @param \Monolog\Logger $logger
     *
     * @return void
     */
    public function setLogger($logger): void
    {
        if (!$this->logger instanceof \Monolog\Logger) {
            throw new QPayException(
                'Logger should be an instance of Monolog Logger.'
            );
        }

        $this->logger = $logger;
    }

    /**
     * Log things that you need to be aware of.
     *
     * @param string $level   The level for the message.
     * @param string $message The message body.
     *
     * @return void
     */
    public function log($level = 'warning', $message = '', $data = []): void
    {
        if (!$this->logger) {
            return;
        }

        switch ($level) {
            case 'debug':
            case 'info':
            case 'notice':
            case 'warning':
            case 'error':
                $this->logger->{$level}($message, $data);
        }
    }
}
