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

namespace App\Data\Seeds;

use App\Data\Models\User as Model;
use Doctrine\DBAL\Exception as DBALException;
use Exception;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Whoa\Contracts\Data\SeedInterface;
use Whoa\Contracts\Data\TimestampFields;
use Whoa\Contracts\Data\UuidFields;
use Whoa\Crypt\Contracts\HasherInterface;
use Whoa\Data\Seeds\SeedTrait;

/**
 * @package App
 */
class UsersSeed implements SeedInterface
{
    use SeedTrait;

    /** @var string Field value */
    public const DEFAULT_PASSWORD = 'p@ssword';

    /** @var int Field value */
    public const ID_DEFAULT_ADMINISTRATOR = 1;
    /** @var string Field value */
    public const ROLE_DEFAULT_ADMINISTRATOR = RolesSeed::ID_ADMINISTRATORS;
    /** @var string Field value */
    public const EMAIL_DEFAULT_ADMINISTRATOR = 'administrator@local.domain';

    /** @var int Field value */
    public const ID_DEFAULT_MODERATOR = 2;
    /** @var string Field value */
    public const ROLE_DEFAULT_MODERATOR = RolesSeed::ID_MODERATORS;
    /** @var string Field value */
    public const EMAIL_DEFAULT_MODERATOR = 'moderator@local.domain';

    /** @var int Field value */
    public const ID_DEFAULT_USER = 3;
    /** @var string Field value */
    public const ROLE_DEFAULT_USER = RolesSeed::ID_USERS;
    /** @var string Field value */
    public const EMAIL_DEFAULT_USER = 'user@local.domain';

    /**
     * @inheritdoc
     * @throws DBALException
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws Exception
     */
    public function run(): void
    {
        $hasher = $this->getContainer()->get(HasherInterface::class);

        $this->seedModelData(Model::class, [
            Model::FIELD_ID => self::ID_DEFAULT_ADMINISTRATOR,
            UuidFields::FIELD_UUID => $this->uuid(),
            Model::FIELD_ID_ROLE => self::ROLE_DEFAULT_ADMINISTRATOR,
            Model::FIELD_EMAIL => self::EMAIL_DEFAULT_ADMINISTRATOR,
            Model::FIELD_PASSWORD_HASH => $hasher->hash(self::DEFAULT_PASSWORD),
            TimestampFields::FIELD_CREATED_AT => $this->now(),
        ]);

        $this->seedModelData(Model::class, [
            Model::FIELD_ID => self::ID_DEFAULT_MODERATOR,
            UuidFields::FIELD_UUID => $this->uuid(),
            Model::FIELD_ID_ROLE => self::ROLE_DEFAULT_MODERATOR,
            Model::FIELD_EMAIL => self::EMAIL_DEFAULT_MODERATOR,
            Model::FIELD_PASSWORD_HASH => $hasher->hash(self::DEFAULT_PASSWORD),
            TimestampFields::FIELD_CREATED_AT => $this->now(),
        ]);

        $this->seedModelData(Model::class, [
            Model::FIELD_ID => self::ID_DEFAULT_USER,
            UuidFields::FIELD_UUID => $this->uuid(),
            Model::FIELD_ID_ROLE => self::ROLE_DEFAULT_USER,
            Model::FIELD_EMAIL => self::EMAIL_DEFAULT_USER,
            Model::FIELD_PASSWORD_HASH => $hasher->hash(self::DEFAULT_PASSWORD),
            TimestampFields::FIELD_CREATED_AT => $this->now(),
        ]);
    }
}
