<?php


namespace App\Services\Marketplace\Exceptions;

use Exception;

class ResponseParsingException extends Exception
{
    /**
     * @var string The error message
     */
    protected $message = 'An error occurred while parsing marketplace response';
}
