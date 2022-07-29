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

use App\Api\RolesApi as Api;
use App\Data\Seeds\RolesSeed as Seed;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Tests\TestCase;

/**
 * @package Tests
 */
class RoleApiTest extends TestCase
{
    /**
     * Shows usage of low level API in tests.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testLowLevelApiRead(): void
    {
        $this->setPreventCommits();

        $this->setModerator();
        $api = $this->createApi(Api::class);

        $roleId = Seed::ID_USERS;
        $this->assertNotNull($api->read((string)$roleId));
    }

    /**
     * Same test but with auth by a OAuth token.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public function testLowLevelApiReadWithAuthByToken(): void
    {
        $this->setPreventCommits();

        $oauthToken = $this->getModeratorOAuthToken();
        $accessToken = $oauthToken->access_token;
        $this->setUserByToken($accessToken);

        $api = $this->createApi(Api::class);

        $roleId = Seed::ID_USERS;
        $this->assertNotNull($api->read((string)$roleId));
    }
}
