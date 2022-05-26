<?php

declare(strict_types=1);

namespace App\Validation\OAuthRedirectUri;

use App\Json\Schemas\OAuthRedirectUriSchema as Schema;
use App\Validation\BaseRules;
use Whoa\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App
 */
final class OAuthRedirectUriRules extends BaseRules
{
    /**
     * @return RuleInterface
     */
    public static function schemaType(): RuleInterface
    {
        return self::equals(Schema::TYPE);
    }
}
