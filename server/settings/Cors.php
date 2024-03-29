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

use Dotenv\Dotenv;
use Whoa\Application\Packages\Cors\CorsSettings;

/**
 * @package Settings
 */
class Cors extends CorsSettings
{
    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        (new Dotenv(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..'])))->load();

        return [
                static::KEY_LOG_IS_ENABLED => filter_var(getenv('APP_ENABLE_LOGS'), FILTER_VALIDATE_BOOLEAN),

                /**
                 * A list of allowed request origins (no trail slashes, no default ports).
                 * For example, [
                 *     'http://example.com:123',
                 * ];
                 */
                static::KEY_ALLOWED_ORIGINS => [
                    'http://localhost:8080',
                ],

                /**
                 * A list of allowed request methods.
                 *
                 * Security Note: you have to remember CORS is not access control system and you should not expect all
                 * cross-origin requests will have pre-flights. For so-called 'simple' methods with so-called 'simple'
                 * headers request will be made without pre-flight. Thus you can not restrict such requests with CORS
                 * and should use other means.
                 * For example method 'GET' without any headers or with only 'simple' headers will not have pre-flight
                 * request so disabling it will not restrict access to resource(s).
                 *
                 * You can read more on 'simple' methods at http://www.w3.org/TR/cors/#simple-method
                 */
                static::KEY_ALLOWED_METHODS => [
                    'GET',
                    'POST',
                    'PATCH',
                    'PUT',
                    'DELETE',
                ],

                /**
                 * A list of allowed request headers.
                 *
                 * Security Note: you have to remember CORS is not access control system and you should not expect all
                 * cross-origin requests will have pre-flights. For so-called 'simple' methods with so-called 'simple'
                 * headers request will be made without pre-flight. Thus you can not restrict such requests with CORS
                 * and should use other means.
                 * For example method 'GET' without any headers or with only 'simple' headers will not have pre-flight
                 * request so disabling it will not restrict access to resource(s).
                 *
                 * You can read more on 'simple' headers at http://www.w3.org/TR/cors/#simple-header
                 */
                static::KEY_ALLOWED_HEADERS => [
                    'Accept',
                    'Content-Type',
                    'Authorization',
                    'Origin',
                ],

                /**
                 * A list of headers (case insensitive) which will be made accessible to
                 * user agent (browser) in response.
                 */
                static::KEY_EXPOSED_HEADERS => [
                    'Content-Type',
                ],

                static::KEY_IS_CHECK_HOST => !filter_var(getenv('APP_IS_DEBUG'), FILTER_VALIDATE_BOOLEAN),

            ] + parent::getSettings();
    }
}
