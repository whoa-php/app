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

namespace App\Routes;

use App\Web\Middleware\CatchAll;
use App\Web\Middleware\CookieAuth;
use Whoa\Contracts\Application\RoutesConfiguratorInterface;
use Whoa\Contracts\Routing\GroupInterface;
use Whoa\Flute\Http\Traits\FluteRoutesTrait;

/**
 * @package App
 */
class WebRoutes implements RoutesConfiguratorInterface
{
    use FluteRoutesTrait;

    /** @var string Web API prefix */
    public const TOP_GROUP_PREFIX = '';

    /**
     * @inheritdoc
     */
    public static function configureRoutes(GroupInterface $routes): void
    {
        // Every group, controller and even method may have custom `Request` factory and `Container` configurator.
        // Thus, container for `API` and `Web` groups can be configured differently which could be used for
        // improving page load time for every HTTP route.
        // Container can be configured even for individual controller method (e.g. `PaymentsController::index`).
        // Also custom middleware could be specified for a group, controller or method.

        $routes
            // HTML pages group
            // This group uses exception handler to provide error information in HTML format with Whoops.
            ->group(self::TOP_GROUP_PREFIX, function (GroupInterface $routes): void {
                $routes->addContainerConfigurators([])
                    ->addMiddleware([]);
            });
    }

    /**
     * This middleware will be executed on every request even when no matching route is found.
     * @return string[]
     */
    public static function getMiddleware(): array
    {
        return [
            CatchAll::class,
            CookieAuth::class,
        ];
    }
}
