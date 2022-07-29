<?php

/**
 * Copyright 2015-2019 info@neomerx.com
 * Modification Copyright 2021-2022 info@whoaphp.com
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

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
