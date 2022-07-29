<?php

/**
 * Copyright 2021-2022 info@whoaphp.com
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
