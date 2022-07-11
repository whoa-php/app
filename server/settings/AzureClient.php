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

namespace Settings;

use Dotenv\Dotenv;
use Whoa\OAuthClient\Package\OAuthClientSettings;

/**
 * @package Settings
 */
class AzureClient extends OAuthClientSettings
{
    /**
     * @inheritDoc
     */
    protected function getSettings(): array
    {
        (new Dotenv(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..'])))->load();

        return [
                self::KEY_PROVIDER_IDENTIFIER => getenv('AZURE_CLIENT_PROVIDER_IDENTIFIER'),
                self::KEY_PROVIDER_NAME => getenv('AZURE_CLIENT_PROVIDER_NAME'),
                self::KEY_CLIENT_IDENTIFIER => getenv('AZURE_CLIENT_IDENTIFIER'),
                self::KEY_TENANT_IDENTIFIER => getenv('AZURE_CLIENT_TENANT_IDENTIFIER'),
                self::KEY_DISCOVERY_DOCUMENT_URI => getenv('AZURE_CLIENT_DISCOVERY_DOCUMENT_URI'),
                self::KEY_JWK_SET_URI_KEY => getenv('AZURE_CLIENT_JWK_SET_URI_KEY'),
            ] + parent::getSettings();
    }
}
