<?php

declare(strict_types=1);

namespace App\Validation\OAuthToken;

use App\Json\Schemas\OAuthTokenSchema as Schema;
use App\Validation\BaseRules;
use Whoa\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App
 */
final class OAuthTokenRules extends BaseRules
{
    /**
     * @return RuleInterface
     */
    public static function schemaType(): RuleInterface
    {
        return self::equals(Schema::TYPE);
    }
}
