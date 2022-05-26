<?php

declare(strict_types=1);

namespace App\Validation;

use Whoa\Flute\Contracts\Validation\ErrorCodes as BaseErrorCodes;

/**
 * @package App
 */
interface ErrorCodes extends BaseErrorCodes
{
    /** Custom error code */
    public const IS_EMAIL = BaseErrorCodes::FLUTE_LAST + 1;

    /** Custom error code */
    public const CONFIRMATION_SHOULD_MATCH_PASSWORD = self::IS_EMAIL + 1;
}
