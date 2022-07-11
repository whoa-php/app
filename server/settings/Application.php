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

use App\AzureClient\AzureClientProvider;
use Dotenv\Dotenv;
use Whoa\Application\Packages\Application\ApplicationProvider;
use Whoa\Application\Packages\Authorization\AuthorizationProvider;
use Whoa\Application\Packages\Cookies\CookieProvider;
use Whoa\Application\Packages\Cors\CorsProvider;
use Whoa\Application\Packages\Csrf\CsrfMinimalProvider;
use Whoa\Application\Packages\Data\DataProvider;
use Whoa\Application\Packages\FileSystem\FileSystemProvider;
use Whoa\Application\Packages\L10n\L10nProvider;
use Whoa\Application\Packages\Monolog\MonologFileProvider;
use Whoa\Contracts\Application\ApplicationConfigurationInterface;
use Whoa\Crypt\Package\HasherProvider;
use Whoa\Flute\Package\FluteProvider;
use Whoa\Passport\Package\PassportProvider;
use Whoa\Templates\Package\TwigTemplatesProvider;

/**
 * @package Settings
 */
class Application implements ApplicationConfigurationInterface
{
    /** @var callable */
    public const CACHE_CALLABLE = '\\Cached\\Application::get';

    /**
     * @inheritdoc
     */
    public function get(): array
    {
        (new Dotenv(implode(DIRECTORY_SEPARATOR, [__DIR__, '..', '..'])))->load();

        $routesMask = '*Routes.php';
        $routesFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Routes']);
        $webCtrlFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Web', 'Controllers']);
        $routesPath = implode(DIRECTORY_SEPARATOR, [$routesFolder, $routesMask]);
        $confPath = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Container', '*.php']);
        $commandsFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Commands']);
        $cacheFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'storage', 'cache', 'settings']);

        $originScheme = getenv('APP_ORIGIN_SCHEME');
        $originHost = getenv('APP_ORIGIN_HOST');
        $originPort = getenv('APP_ORIGIN_PORT');
        $originUri = filter_var("$originScheme://$originHost:$originPort", FILTER_VALIDATE_URL);
        assert(is_string($originUri) === true);

        return [
            static::KEY_APP_NAME => getenv('APP_NAME'),
            static::KEY_IS_LOG_ENABLED => filter_var(getenv('APP_ENABLE_LOGS'), FILTER_VALIDATE_BOOLEAN),
            static::KEY_IS_DEBUG => filter_var(getenv('APP_IS_DEBUG'), FILTER_VALIDATE_BOOLEAN),
            static::KEY_ROUTES_FILE_MASK => $routesMask,
            static::KEY_ROUTES_FOLDER => $routesFolder,
            static::KEY_WEB_CONTROLLERS_FOLDER => $webCtrlFolder,
            static::KEY_ROUTES_PATH => $routesPath,
            static::KEY_CONTAINER_CONFIGURATORS_PATH => $confPath,
            static::KEY_CACHE_FOLDER => $cacheFolder,
            static::KEY_CACHE_CALLABLE => static::CACHE_CALLABLE,
            static::KEY_COMMANDS_FOLDER => $commandsFolder,
            static::KEY_APP_ORIGIN_SCHEMA => $originScheme,
            static::KEY_APP_ORIGIN_HOST => $originHost,
            static::KEY_APP_ORIGIN_PORT => $originPort,
            static::KEY_APP_ORIGIN_URI => $originUri,
            static::KEY_PROVIDER_CLASSES => [
                ApplicationProvider::class,
                AuthorizationProvider::class,
                //\Whoa\Application\Packages\PDO\PdoProvider::class,
                CookieProvider::class,
                CorsProvider::class,
                CsrfMinimalProvider::class,
                DataProvider::class,
                L10nProvider::class,
                MonologFileProvider::class,
                FileSystemProvider::class,
                //\Whoa\Application\Packages\Session\SessionProvider::class,
                HasherProvider::class,
                //\Whoa\Crypt\Package\SymmetricCryptProvider::class,
                //\Whoa\Crypt\Package\AsymmetricPublicEncryptPrivateDecryptProvider::class,
                //\Whoa\Crypt\Package\AsymmetricPrivateEncryptPublicDecryptProvider::class,
                //\Whoa\Events\Package\EventProvider::class,
                FluteProvider::class,
                PassportProvider::class,
                TwigTemplatesProvider::class,
                AzureClientProvider::class,
            ],
        ];
    }
}
