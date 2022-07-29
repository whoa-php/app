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

namespace Settings;

use App\Json\Exceptions\ThrowableConverter;
use App\Routes\ApiRoutes;
use Whoa\Application\Exceptions\AuthorizationException;
use Whoa\Flute\Package\FluteSettings;

/**
 * @package Settings
 */
class ApplicationApi extends FluteSettings
{
    /** @inheritdoc */
    public const DEFAULT_PAGE_SIZE = 10;

    /** @inheritdoc */
    public const DEFAULT_MAX_PAGE_SIZE = 30;

    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        $defaults = parent::getSettings();

        $apiFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Api']);
        $schemasFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Json', 'Schemas']);
        $jsonCtrlFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Json', 'Controllers']);
        $valRulesFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Validation', '**']);
        $jsonValFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Validation', '**']);
        $formValFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Validation', '**']);
        $queryValFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Validation', '**']);

        return [

                static::KEY_URI_PREFIX => ApiRoutes::API_URI_PREFIX,
                static::KEY_THROWABLE_TO_JSON_API_EXCEPTION_CONVERTER => ThrowableConverter::class,
                static::KEY_API_FOLDER => $apiFolder,
                static::KEY_JSON_CONTROLLERS_FOLDER => $jsonCtrlFolder,
                static::KEY_SCHEMAS_FOLDER => $schemasFolder,
                static::KEY_JSON_VALIDATION_RULES_FOLDER => $valRulesFolder,
                static::KEY_JSON_VALIDATORS_FOLDER => $jsonValFolder,
                static::KEY_FORM_VALIDATORS_FOLDER => $formValFolder,
                static::KEY_QUERY_VALIDATORS_FOLDER => $queryValFolder,
                static::KEY_JSON_ENCODE_OPTIONS => $defaults[static::KEY_JSON_ENCODE_OPTIONS] | JSON_PRETTY_PRINT,
                static::KEY_DO_NOT_LOG_EXCEPTIONS_LIST => [

                        AuthorizationException::class,

                    ] + $defaults[static::KEY_DO_NOT_LOG_EXCEPTIONS_LIST],

            ] + $defaults;
    }
}
