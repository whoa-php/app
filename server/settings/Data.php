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

use Faker\Generator;
use Whoa\Application\Packages\Data\DataSettings;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

/**
 * @package Settings
 */
class Data extends DataSettings
{
    /**
     * @inheritdoc
     */
    protected function getSettings(): array
    {
        $modelsFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Data', 'Models']);
        $migrationsFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Data', 'Migrations']);
        $seedsFolder = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Data', 'Seeds']);
        $migrationsList = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Data', 'migrations.php']);
        $seedsList = implode(DIRECTORY_SEPARATOR, [__DIR__, '..', 'app', 'Data', 'seeds.php']);

        return [
                static::KEY_MODELS_FOLDER => $modelsFolder,
                static::KEY_MIGRATIONS_FOLDER => $migrationsFolder,
                static::KEY_MIGRATIONS_LIST_FILE => $migrationsList,
                static::KEY_SEEDS_FOLDER => $seedsFolder,
                static::KEY_SEEDS_LIST_FILE => $seedsList,
                static::KEY_SEED_INIT => [static::class, 'resetFaker'],
            ] + parent::getSettings();
    }

    /**
     * @param ContainerInterface $container
     * @param string $seedClass
     * @return void
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function resetFaker(ContainerInterface $container, string $seedClass)
    {
        /** @var Generator $faker */
        $faker = $container->get(Generator::class);
        $faker->seed(crc32($seedClass));
    }
}
