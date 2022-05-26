<?php

declare(strict_types=1);

namespace App\Validation\User;

use App\Data\Models\User as Model;
use App\Json\Schemas\UserSchema as Schema;
use App\Validation\BaseRules;
use Whoa\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App
 */
final class UserRules extends BaseRules
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
    public static function email(bool $isUpdate = false): RuleInterface
    {
        $isUnique = self::unique(
            Model::TABLE_NAME,
            Model::FIELD_EMAIL,
            $isUpdate === false ? null : Model::FIELD_ID
        );
        $maxLength = Model::getAttributeLengths()[Model::FIELD_EMAIL];

        return self::asSanitizedEmail(
            self::stringLengthBetween(
                1,
                $maxLength,
                $isUnique
            )
        );
    }

    /**
     * @return RuleInterface
     */
    public static function password(): RuleInterface
    {
        $maxLength = Model::getAttributeLengths()[Model::FIELD_PASSWORD_HASH];

        return self::isString(
            self::stringLengthBetween(
                Model::MIN_PASSWORD_LENGTH,
                $maxLength
            )
        );
    }
}
