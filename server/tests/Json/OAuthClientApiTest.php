<?php

/**
 * Copyright 2021-2022 info@whoaphp.com
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

use App\Data\Seeds\PassportSeed;
use App\Json\Schemas\OAuthClientSchema as Schema;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\DBAL\Exception as DBALException;
use Whoa\Contracts\Data\TimestampFields;
use Whoa\Contracts\Http\ThrowableResponseInterface;
use Whoa\Passport\Contracts\Models\ClientModelInterface as ModelInterface;
use Whoa\Testing\JsonApiCallsTrait;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Tests\TestCase;

/**
 * @package Tests
 */
class OAuthClientApiTest extends TestCase
{
    use JsonApiCallsTrait;

    public const API_URI = '/api/v1/' . Schema::TYPE;

    /**
     * Test API index.
     */
    public function testIndex()
    {
        $this->setPreventCommits();

        $response = $this->get(self::API_URI, [], $this->getAdministratorOAuthHeader());
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertCount(1, $json->data);
    }

    /**
     * Test API index with parameters.
     */
    public function testIndexWithParameter()
    {
        $this->setPreventCommits();

        $clientIdentifier = 'default_client';
        $scopeIdentifier = PassportSeed::SCOPE_IDENTIFIER_OAUTH_WRITE;
        $queryParams = [
            'filter' => [
                'identifier' => [
                    'eq' => "$clientIdentifier",
                ],
                'oauth-scopes.identifier' => [
                    'in' => "$scopeIdentifier",
                    // example how conditions could be applied to relationships' attributes
                ],
            ],
            'sort' => '+id,uuid,oauth-scopes,oauth-scopes.id',
            // example of how multiple sorting conditions could be applied
            'include' => 'oauth-scopes',
            'fields' => [
                'oauth-clients' => 'id,uuid,identifier,oauth-scopes',
            ],
        ];

        $headers = $this->getAdministratorOAuthHeader();
        $response = $this->get(self::API_URI, $queryParams, $headers);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resources = json_decode((string)$response->getBody()));
        $this->assertCount(1, $resources->data);

        $resource = $resources->data[0];
        $this->assertEquals(1, $resource->id);
        $this->assertEquals($clientIdentifier, $resource->attributes->identifier);
        $this->assertEquals(1, $resource->relationships->{'oauth-scopes'}->data[0]->id);

        // check response has included posts as well
        $this->assertCount(5, $resources->included);
        $resource = $resources->included[0];
        $this->assertEquals('oauth-scopes', $resource->type);
        $this->assertEquals($scopeIdentifier, $resource->attributes->identifier);
    }

    /**
     * Test API index with invalid token.
     */
    public function testIndexInvalidToken()
    {
        $response = $this->get(self::API_URI, [], $this->getOAuthHeader('XXX'));
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * Test read API
     */
    public function testRead()
    {
        $this->setPreventCommits();

        $clientId = 1;
        $response = $this->get(self::API_URI . "/$clientId", [], $this->getAdministratorOAuthHeader());
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertEquals($clientId, $json->data->id);
        $this->assertEquals(Schema::TYPE, $json->data->type);
    }

    /**
     * Test API delete.
     */
    public function testDelete()
    {
        $this->setPreventCommits();

        $id = 1;
        $headers = $this->getAdministratorOAuthHeader();

        // check resource exists
        $response = $this->get(self::API_URI . "/$id", [], $headers);
        $this->assertEquals(200, $response->getStatusCode());

        // delete
        $response = $this->delete(self::API_URI . "/$id", [], $headers);
        $this->assertEquals(204, $response->getStatusCode());

        // check resource do not exist (catch-all route returning 200)
        $response = $this->get(self::API_URI . "/$id", [], $headers);
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * Test API Create
     * @throws DBALException
     * @throws DBALDriverException
     */
    public function testCreate()
    {
        $this->setPreventCommits();

        $identifier = 'test_client';
        $name = 'Test client';
        $description = 'OAuth client for testing';
        $jsonInput = <<<EOT
        {
            "data" : {
                "type" : "oauth-clients",
                "attributes" : {
                    "identifier": "$identifier",
                    "name"      : "$name",
                    "description": "$description"
                }
            }
        }
EOT;
        $headers = $this->getAdministratorOAuthHeader();

        $response = $this->postJsonApi(self::API_URI, $jsonInput, $headers);
        $this->assertEquals(201, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $id = $json->data->id;

        // check user exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$id", [], $headers)->getStatusCode());

        // ... or make same check in the database
        $query = $this->getCapturedConnection()->createQueryBuilder();
        $statement = $query
            ->select('*')
            ->from(ModelInterface::TABLE_NAME)
            ->where(ModelInterface::FIELD_ID . '=' . $query->createPositionalParameter($id))
            ->execute();
        $this->assertNotEmpty($statement->fetchAssociative());
    }

    /**
     * Test API create invalid data
     */
    public function testCreateInvalidData()
    {
        $this->setPreventCommits();

        $name = "Invalid Client";
        $jsonInput = <<<EOT
        {
            "data" : {
                "type" : "oauth-clients",
                "attributes" : {
                    "name": "$name"
                }
            }
        }
EOT;
        /** @var ThrowableResponseInterface $response */
        $this->assertInstanceOf(
            ThrowableResponseInterface::class,
            $response = $this->postJsonApi(self::API_URI, $jsonInput, $this->getAdministratorOAuthHeader())
        );
        /** @var JsonApiException $exception */
        $this->assertInstanceOf(JsonApiException::class, $exception = $response->getThrowable());

        $this->assertCount(1, $exception->getErrors());
        $error1 = $exception->getErrors()->getArrayCopy()[0];
        $this->assertEquals('The value is required.', $error1->getDetail());
    }

    /**
     * Test API update
     * @throws DBALException
     * @throws DBALDriverException
     */
    public function testUpdate()
    {
        $this->setPreventCommits();

        $oldId = 1;
        $newIdentifier = 'test_client';
        $newName = 'Test Client';
        $jsonInput = <<<EOT
        {
            "data" : {
                "type" : "oauth-clients",
                "id"   : "$oldId",
                "attributes" : {
                    "identifier": "$newIdentifier",
                    "name": "$newName"
                }
            }
        }
EOT;
        $headers = $this->getAdministratorOAuthHeader();

        $response = $this->patchJsonApi(self::API_URI . "/$oldId", $jsonInput, $headers);
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertEquals($oldId, $json->data->id);

        // check user exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$oldId", [], $headers)->getStatusCode());

        // ... or make same check in the database
        $query = $this->getCapturedConnection()->createQueryBuilder();
        $statement = $query
            ->select('*')
            ->from(ModelInterface::TABLE_NAME)
            ->where(ModelInterface::FIELD_ID . '=' . $query->createPositionalParameter($oldId))
            ->execute();
        $this->assertNotEmpty($values = $statement->fetchAssociative());
        $this->assertEquals($newIdentifier, $values[ModelInterface::FIELD_IDENTIFIER]);
        $this->assertEquals($newName, $values[ModelInterface::FIELD_NAME]);
        $this->assertNotEmpty($values[TimestampFields::FIELD_UPDATED_AT]);
    }

    /**
     * Test User's API.
     */
    public function testUnauthorizedDenied()
    {
        // no token header

        /** @var ThrowableResponseInterface $response */
        $this->assertInstanceOf(
            ThrowableResponseInterface::class,
            $response = $this->get(self::API_URI)
        );
        /** @var JsonApiException $exception */
        $this->assertInstanceOf(JsonApiException::class, $exception = $response->getThrowable());

        $this->assertCount(1, $exception->getErrors());
        $error = $exception->getErrors()->getArrayCopy()[0];
        $this->assertEquals('You are not authorized for action `canViewOAuthClients`.', $error->getDetail());
    }
}
