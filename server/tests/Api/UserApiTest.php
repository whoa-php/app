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

namespace Tests\Api;

use App\Api\UsersApi as Api;
use App\Data\Models\User as Model;
use App\Data\Seeds\UsersSeed as Seed;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\DBAL\Exception as DBALException;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Whoa\Contracts\Exceptions\AuthorizationExceptionInterface;
use Tests\TestCase;

use function assert;

/**
 * @package Tests
 */
class UserApiTest extends TestCase
{
    /**
     * Sample how to test low level API.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DBALException
     * @throws DBALDriverException
     */
    public function testLowLevelApi()
    {
        $this->setPreventCommits();

        // create API
        $api = $this->createUsersApi();

        // Call and check any method from low level API.

        /** Default seed data. Manually checked. */
        $this->assertEquals(3, $api->noAuthReadUserIdByEmail(Seed::EMAIL_DEFAULT_USER));
    }

    /**
     * Test for password reset.
     * @throws AuthorizationExceptionInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws DBALException
     */
    public function testResetPassword()
    {
        $this->setPreventCommits();

        // create APIs

        $noAuthApi = $this->createUsersApi();

        $this->setAdministrator();
        $api = $this->createUsersApi();

        // Call reset method.
        $userId = 1;
        $before = $api->read((string)$userId);
        $this->assertTrue($noAuthApi->noAuthResetPassword($userId, 'new password'));
        $after = $api->read((string)$userId);
        $this->assertNotEquals($before->{Model::FIELD_PASSWORD_HASH}, $after->{Model::FIELD_PASSWORD_HASH});
    }

    /**
     * @return Api
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    private function createUsersApi(): Api
    {
        $api = $this->createApi(Api::class);
        assert($api instanceof Api);

        return $api;
    }
}
