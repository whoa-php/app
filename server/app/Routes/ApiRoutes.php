<?php

declare(strict_types=1);

namespace App\Routes;

use App\Json\Controllers\OAuthClientsController;
use App\Json\Controllers\OAuthRedirectUrisController;
use App\Json\Controllers\OAuthScopesController;
use App\Json\Controllers\OAuthTokensController;
use App\Json\Controllers\RolesController;
use App\Json\Controllers\UsersController;
use App\Json\Schemas\OAuthClientSchema;
use App\Json\Schemas\OAuthRedirectUriSchema;
use App\Json\Schemas\OAuthScopeSchema;
use App\Json\Schemas\OAuthTokenSchema;
use App\Json\Schemas\RoleSchema;
use App\Json\Schemas\UserSchema;
use Whoa\Contracts\Application\RoutesConfiguratorInterface;
use Whoa\Contracts\Routing\GroupInterface;
use Whoa\Flute\Http\Traits\FluteRoutesTrait;
use Whoa\Flute\Package\FluteContainerConfigurator;

/**
 * @package App
 */
class ApiRoutes implements RoutesConfiguratorInterface
{
    use FluteRoutesTrait;

    /** @var string API URI prefix */
    public const API_URI_PREFIX = '/api/v1';

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
            // JSON API group
            // This group uses custom exception handler to provide error information in JSON API format.
            ->group(self::API_URI_PREFIX, function (GroupInterface $routes): void {
                $routes->addContainerConfigurators([
                    FluteContainerConfigurator::CONFIGURE_EXCEPTION_HANDLER,
                ]);

                self::apiController($routes, UserSchema::TYPE, UsersController::class);
                self::relationship(
                    $routes,
                    UserSchema::TYPE,
                    UserSchema::REL_ROLE,
                    UsersController::class,
                    'readRole'
                );
                self::relationship(
                    $routes,
                    UserSchema::TYPE,
                    UserSchema::REL_OAUTH_TOKENS,
                    UsersController::class,
                    'readOAuthTokens'
                );

                self::apiController(
                    $routes,
                    RoleSchema::TYPE,
                    RolesController::class
                );
                self::relationship(
                    $routes,
                    RoleSchema::TYPE,
                    RoleSchema::REL_USERS,
                    RolesController::class,
                    'readUsers'
                );

                self::apiController(
                    $routes,
                    OAuthClientSchema::TYPE,
                    OAuthClientsController::class
                );
                self::relationship(
                    $routes,
                    OAuthClientSchema::TYPE,
                    OAuthClientSchema::REL_REDIRECT_URIS,
                    OAuthClientsController::class,
                    'readOAuthRedirectUris'
                );
                self::relationship(
                    $routes,
                    OAuthClientSchema::TYPE,
                    OAuthClientSchema::REL_TOKENS,
                    OAuthClientsController::class,
                    'readOAuthTokens'
                );
                self::relationship(
                    $routes,
                    OAuthClientSchema::TYPE,
                    OAuthClientSchema::REL_SCOPES,
                    OAuthClientsController::class,
                    'readOAuthScopes'
                );

                self::apiController(
                    $routes,
                    OAuthRedirectUriSchema::TYPE,
                    OAuthRedirectUrisController::class
                );
                self::relationship(
                    $routes,
                    OAuthRedirectUriSchema::TYPE,
                    OAuthRedirectUriSchema::REL_CLIENT,
                    OAuthRedirectUrisController::class,
                    'readOAuthClient'
                );

                self::apiController(
                    $routes,
                    OAuthScopeSchema::TYPE,
                    OAuthScopesController::class
                );
                self::relationship(
                    $routes,
                    OAuthScopeSchema::TYPE,
                    OAuthScopeSchema::REL_CLIENTS,
                    OAuthScopesController::class,
                    'readOAuthClients'
                );
                self::relationship(
                    $routes,
                    OAuthScopeSchema::TYPE,
                    OAuthScopeSchema::REL_TOKENS,
                    OAuthScopesController::class,
                    'readOAuthTokens'
                );

                self::apiController(
                    $routes,
                    OAuthTokenSchema::TYPE,
                    OAuthTokensController::class
                );
                self::relationship(
                    $routes,
                    OAuthTokenSchema::TYPE,
                    OAuthTokenSchema::REL_USER,
                    OAuthTokensController::class,
                    'readOAuthUser'
                );
                self::relationship(
                    $routes,
                    OAuthTokenSchema::TYPE,
                    OAuthTokenSchema::REL_CLIENT,
                    OAuthTokensController::class,
                    'readOAuthClient'
                );
                self::relationship(
                    $routes,
                    OAuthTokenSchema::TYPE,
                    OAuthTokenSchema::REL_SCOPES,
                    OAuthTokensController::class,
                    'readOAuthScopes'
                );
            });
    }

    /**
     * This middleware will be executed on every request even when no matching route is found.
     *
     * @return string[]
     */
    public static function getMiddleware(): array
    {
        return [];
    }
}
