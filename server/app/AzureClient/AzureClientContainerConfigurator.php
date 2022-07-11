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

use Psr\Container\ContainerInterface as PsrContainerInterface;
use Settings\AzureClient as C;
use Whoa\Contracts\Container\ContainerInterface;
use Whoa\Contracts\Settings\Packages\OAuthClientSettingsInterface;
use Whoa\Contracts\Settings\SettingsProviderInterface;
use Whoa\OAuthClient\Clients\Azure\Version2\AzureClient;
use Whoa\OAuthClient\Package\OAuthClientContainerConfigurator;

/**
 * @package App
 */
class AzureClientContainerConfigurator extends OAuthClientContainerConfigurator
{
    /**
     * @inheritDoc
     */
    public static function configureContainer(ContainerInterface $container): void
    {
        $container[AzureClientInterface::class] = function (PsrContainerInterface $container) {
            $settingsProvider = $container->get(SettingsProviderInterface::class);
            $settings = $settingsProvider->get(C::class);

            return (new AzureClient())
                ->setProviderIdentifier($settings[OAuthClientSettingsInterface::KEY_PROVIDER_IDENTIFIER])
                ->setProviderName($settings[OAuthClientSettingsInterface::KEY_PROVIDER_NAME])
                ->setClientIdentifier($settings[OAuthClientSettingsInterface::KEY_CLIENT_IDENTIFIER])
                ->setTenantIdentifier($settings[OAuthClientSettingsInterface::KEY_TENANT_IDENTIFIER])
                ->setDiscoveryDocumentUri($settings[OAuthClientSettingsInterface::KEY_DISCOVERY_DOCUMENT_URI])
                ->setJwkSetUriKey($settings[OAuthClientSettingsInterface::KEY_JWK_SET_URI_KEY]);
        };
    }
}
