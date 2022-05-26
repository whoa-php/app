<?php

declare(strict_types=1);

namespace App\Validation\L10n;

/**
 * @package App
 */
interface Messages extends \Whoa\Flute\L10n\Messages
{
    /** @var string Validation Message Template */
    public const IS_EMAIL = 'The value should be a valid email address.';

    /** @var string Validation Message Template */
    public const CONFIRMATION_SHOULD_MATCH_PASSWORD = 'Passwords should match.';
}
