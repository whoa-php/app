<?php

declare(strict_types=1);

namespace App\Validation\OAuthScope;

use App\Data\Models\OAuthScope as Model;
use App\Json\Schemas\OAuthScopeSchema as Schema;
use App\Validation\BaseRules;
use Whoa\Passport\Contracts\Models\ScopeModelInterface as ModelInterface;
use Whoa\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App
 */
final class OAuthScopeRules extends BaseRules
{
    /**
     * @return RuleInterface
     */
    public static function schemaType(): RuleInterface
    {
        return self::equals(Schema::TYPE);
    }

    /**
     * @param bool $isUpdate
     * @return RuleInterface
     */
    public static function identifier(bool $isUpdate = false): RuleInterface
    {
        $isUnique = self::unique(
            ModelInterface::TABLE_NAME,
            ModelInterface::FIELD_IDENTIFIER,
            $isUpdate === false ? null : ModelInterface::FIELD_ID
        );
        $maxLength = Model::getAttributeLengths()[ModelInterface::FIELD_IDENTIFIER];

        return self::asSanitizedString(
            self::stringLengthBetween(
                1,
                $maxLength,
                $isUnique
            )
        );
    }

    /**
     * @param bool $isUpdate
     *
     * @return RuleInterface
     */
    public static function name(bool $isUpdate = false): RuleInterface
    {
        $isUnique = self::unique(
            ModelInterface::TABLE_NAME,
            ModelInterface::FIELD_NAME,
            $isUpdate === false ? null : ModelInterface::FIELD_ID
        );
        $maxLength = Model::getAttributeLengths()[ModelInterface::FIELD_NAME];

        return self::asSanitizedString(
            self::stringLengthBetween(
                1,
                $maxLength,
                $isUnique
            )
        );
    }
}
