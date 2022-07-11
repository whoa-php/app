<?php

namespace App\AzureClient;

use Exception;
use Whoa\OAuthClient\Exceptions\RuntimeException;

final class AzureClientException extends RuntimeException
{
    /** @var string Error key */
    public const ERROR_CREATE_LOG_IN_ACTIVITY = 'error_create_log_in_activity';

    /** @var string Error key */
    public const ERROR_MISSING_ID_TOKEN = 'error_missing_id_token';

    /** @var string Error key */
    public const ERROR_LOG_IN = 'error_log_in';

    /** @var string[] Error messages */
    public const ADDITIONAL_MESSAGES = [
        self::ERROR_CREATE_LOG_IN_ACTIVITY => 'Creating log in activity failed.',
        self::ERROR_MISSING_ID_TOKEN => 'Missing Azure ID token.',
        self::ERROR_LOG_IN => 'Unable to log in, either account does not exist or has been suspended.',
    ];

    /**
     * @inheritDoc
     */
    public function __construct(
        string $errorCode,
        string $errorUri = null,
        int $httpCode = 400,
        array $httpHeaders = [],
        array $descriptions = null,
        Exception $previous = null
    ) {
        $errorMessages = RuntimeException::DEFAULT_MESSAGES + self::ADDITIONAL_MESSAGES;
        $descriptions = $descriptions === null ? $errorMessages : $descriptions;

        parent::__construct($errorCode, $errorUri, $httpCode, $httpHeaders, $descriptions, $previous);
    }
}
