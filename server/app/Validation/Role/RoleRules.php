<?php

declare(strict_types=1);

namespace App\Validation\Role;

use App\Data\Models\Role as Model;
use App\Json\Schemas\RoleSchema as Schema;
use App\Validation\BaseRules;
use Whoa\Validation\Contracts\Rules\RuleInterface;

/**
 * @package App
 */
final class RoleRules extends BaseRules
{
    /**
     * @return RuleInterface
     */
    public static function schemaType(): RuleInterface
    {
        return self::equals(Schema::TYPE);
    }

    /**
     * @param bool $onUpdate
     *
     * @return RuleInterface
     */
    public static function name(bool $onUpdate = false): RuleInterface
    {
        $isUnique = self::unique(
            Model::TABLE_NAME,
            Model::FIELD_NAME,
            $onUpdate === false ? null : Model::FIELD_ID
        );
        $maxLength = Model::getAttributeLengths()[Model::FIELD_NAME];

        return self::asSanitizedString(
            self::stringLengthBetween(
                1,
                $maxLength,
                $isUnique
            )
        );
    }
}
