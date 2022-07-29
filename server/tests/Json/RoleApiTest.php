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

namespace Tests\Json;

use App\Data\Models\Role as Model;
use App\Data\Seeds\RolesSeed as Seed;
use App\Json\Schemas\RoleSchema as Schema;
use Doctrine\DBAL\Exception as DBALException;
use Whoa\Contracts\Data\TimestampFields;
use Whoa\Testing\JsonApiCallsTrait;
use Tests\TestCase;

/**
 * @package Tests
 */
class RoleApiTest extends TestCase
{
    use JsonApiCallsTrait;

    public const API_URI = '/api/v1/' . Schema::TYPE;

    /**
     * Test Role's API.
     */
    public function testIndex()
    {
        $this->setPreventCommits();

        $response = $this->get(self::API_URI, [], $this->getModeratorOAuthHeader());
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertCount(3, $json->data);
    }

    /**
     * Test Role's API.
     */
    public function testRead()
    {
        $this->setPreventCommits();

        $roleId = Seed::ID_USERS;
        $response = $this->get(self::API_URI . "/$roleId", [], $this->getModeratorOAuthHeader());
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertEquals($roleId, $json->data->id);
        $this->assertEquals(Schema::TYPE, $json->data->type);
    }

    /**
     * Test Role's API.
     */
    public function testReadRelationships()
    {
        $this->setPreventCommits();

        $roleId = Seed::ID_USERS;
        $response = $this->get(self::API_URI . "/$roleId/users", [], $this->getModeratorOAuthHeader());
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertCount(1, $json->data);
    }

    /**
     * Test Role's API.
     * @throws DBALException
     */
    public function testCreate()
    {
        $this->setPreventCommits();

        $name = "New role";
        $jsonInput = <<<EOT
        {
            "data" : {
                "type"  : "roles",
                "attributes" : {
                    "name"  : "$name"
                }
            }
        }
EOT;
        $headers = $this->getAdministratorOAuthHeader();

        $response = $this->postJsonApi(self::API_URI, $jsonInput, $headers);
        $this->assertEquals(201, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $roleId = $json->data->id;

        // check role exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$roleId", [], $headers)->getStatusCode());

        // ... or make same check in the database
        $query = $this->getCapturedConnection()->createQueryBuilder();
        $statement = $query
            ->select('*')
            ->from(Model::TABLE_NAME)
            ->where(Model::FIELD_ID . '=' . $query->createPositionalParameter($roleId))
            ->execute();
        $this->assertNotEmpty($statement->fetch());
    }

    /**
     * Test Role's API.
     * @throws DBALException
     */
    public function testUpdate()
    {
        $this->setPreventCommits();

        $index = Seed::ID_USERS;
        $description = "New description";
        $jsonInput = <<<EOT
        {
            "data" : {
                "type"  : "roles",
                "id"    : "$index",
                "attributes" : {
                    "description" : "$description"
                }
            }
        }
EOT;
        $headers = $this->getAdministratorOAuthHeader();

        $response = $this->patchJsonApi(self::API_URI . "/$index", $jsonInput, $headers);
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertEquals($index, $json->data->id);

        // check role exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$index", [], $headers)->getStatusCode());

        // ... or make same check in the database
        $query = $this->getCapturedConnection()->createQueryBuilder();
        $statement = $query
            ->select('*')
            ->from(Model::TABLE_NAME)
            ->where(Model::FIELD_ID . '=' . $query->createPositionalParameter($index))
            ->execute();
        $this->assertNotEmpty($values = $statement->fetch());
        $this->assertEquals($description, $values[Model::FIELD_DESCRIPTION]);
        $this->assertNotEmpty($values[TimestampFields::FIELD_UPDATED_AT]);
    }
}
