<?php


namespace App\Services\Marketplace\Exceptions;

use Exception;

class InvalidConfigException extends Exception
{
    /**
     * @var string The error message
     */
    protected $message = 'Provided marketplace config is invalid';
}
