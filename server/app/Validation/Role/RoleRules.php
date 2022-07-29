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
