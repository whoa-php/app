<?php

declare(strict_types=1);

namespace App\Data\Models;

use Whoa\Contracts\Data\TimestampFields;
use Whoa\Contracts\Data\UuidFields;

/**
 * @package App
 */
interface CommonFields extends UuidFields, TimestampFields
{
}
