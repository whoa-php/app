<?php

declare(strict_types=1);

namespace App\Routes;

use App\Commands\Middleware\CliAuthenticationMiddleware;
use App\Container\CliCommandsConfigurator;
use Whoa\Application\Commands\DataCommand;
use Whoa\Contracts\Commands\RoutesConfiguratorInterface;
use Whoa\Contracts\Commands\RoutesInterface;

/**
 * @package App
 */
class CliRoutes implements RoutesConfiguratorInterface
{
    /**
     * @inheritdoc
     */
    public static function configureRoutes(RoutesInterface $routes): void
    {
        $routes
            ->addGlobalContainerConfigurators([
                CliCommandsConfigurator::CONFIGURATOR,
            ])
            ->addCommandMiddleware(DataCommand::NAME, [CliAuthenticationMiddleware::CALLABLE_HANDLER]);
    }
}
