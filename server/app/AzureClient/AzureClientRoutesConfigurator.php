<?php

/**
 * Copyright 2021 info@whoaphp.com
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

namespace App\AzureClient;

use Whoa\Contracts\Routing\GroupInterface;
use Whoa\OAuthClient\Package\OAuthClientRoutesConfigurator;

/**
 * @package App
 */
class AzureClientRoutesConfigurator extends OAuthClientRoutesConfigurator
{
    /** @var string Route group prefix */
    public const GROUP_PREFIX = 'azure';

    /** @var string Token exchange URI */
    public const TOKEN_URI = 'token';

    /**
     * @inheritDoc
     */
    public static function getMiddleware(): array
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public static function configureRoutes(GroupInterface $routes): void
    {
        $routes->group(static::GROUP_PREFIX, function (GroupInterface $routes) {
            $routes->post(static::TOKEN_URI, AzureClientController::TOKEN_HANDLER);
        });
    }
}
