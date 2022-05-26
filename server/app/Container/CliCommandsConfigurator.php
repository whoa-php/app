<?php

declare(strict_types=1);

namespace App\Container;

use Faker\Factory;
use Faker\Generator;
use Whoa\Contracts\Commands\ContainerConfiguratorInterface;
use Whoa\Contracts\Container\ContainerInterface as WhoaContainerInterface;

/**
 * @package Settings
 */
class CliCommandsConfigurator implements ContainerConfiguratorInterface
{
    /** @var callable */
    public const CONFIGURATOR = [self::class, self::CONTAINER_METHOD_NAME];

    /**
     * @inheritdoc
     */
    public static function configureContainer(WhoaContainerInterface $container): void
    {
        $container[Generator::class] = function () {
            return Factory::create(Factory::DEFAULT_LOCALE);
        };
    }
}
