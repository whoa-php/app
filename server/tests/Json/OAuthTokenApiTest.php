<?php

declare(strict_types=1);

namespace Tests\Json;

use App\Data\Seeds\PassportSeed;
use App\Json\Schemas\OAuthTokenSchema as Schema;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\DBAL\Exception as DBALException;
use Whoa\Contracts\Data\TimestampFields;
use Whoa\Contracts\Http\ThrowableResponseInterface;
use Whoa\Passport\Contracts\Models\TokenModelInterface as ModelInterface;
use Whoa\Testing\JsonApiCallsTrait;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Tests\TestCase;

/**
 * @package Tests
 */
class OAuthTokenApiTest extends TestCase
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
        $queryParams = [
            'filter' => [
                'oauth-client.identifier' => [
                    'in' => "$clientIdentifier",
                    // example how conditions could be applied to relationships' attributes
                ],
            ],
            'sort' => '+id,value,oauth-client,oauth-client.id',
            // example of how multiple sorting conditions could be applied
            'include' => 'oauth-client',
            'fields' => [
                'oauth-tokens' => 'id,uuid,oauth-client',
            ],
        ];

        $headers = $this->getAdministratorOAuthHeader();
        $response = $this->get(self::API_URI, $queryParams, $headers);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resources = json_decode((string)$response->getBody()));
        $this->assertCount(1, $resources->data);

        $resource = $resources->data[0];
        $this->assertEquals(1, $resource->relationships->{'oauth-client'}->data->id);

        // check response has included posts as well
        $this->assertCount(1, $resources->included);
        $resource = $resources->included[0];
        $this->assertEquals('oauth-clients', $resource->type);
        $this->assertEquals($clientIdentifier, $resource->attributes->identifier);
    }

    /**
     * Test API index with invalid token.
     */
    public function testIndexInvalidToken()
    {
        $response = $this->get(self::API_URI, [], $this->getOAuthHeader('XXX'));
        $this->assertEquals(401, $response->getStatusCode());
    }

//    /**
//     * Test read API
//     */
//    public function testRead()
//    {
//        $this->setPreventCommits();
//
//        $id = 1;
//        $response = $this->get(self::API_URI . "/$id", [], $this->getAdministratorOAuthHeader());
//        $this->assertEquals(200, $response->getStatusCode());
//
//        $json = json_decode((string)$response->getBody());
//        $this->assertObjectHasAttribute('data', $json);
//        $this->assertEquals($id, $json->data->id);
//        $this->assertEquals(Schema::TYPE, $json->data->type);
//    }
//
//    /**
//     * Test API delete.
//     */
//    public function testDelete()
//    {
//        $this->setPreventCommits();
//
//        $id = 1;
//        $headers = $this->getAdministratorOAuthHeader();
//
//        // check resource exists
//        $response = $this->get(self::API_URI . "/$id", [], $headers);
//        $this->assertEquals(200, $response->getStatusCode());
//
//        // delete
//        $response = $this->delete(self::API_URI . "/$id", [], $headers);
//        $this->assertEquals(204, $response->getStatusCode());
//
//        // check resource do not exist (catch-all route returning 200)
//        $response = $this->get(self::API_URI . "/$id", [], $headers);
//        $this->assertEquals(403, $response->getStatusCode());
//    }
//
//    /**
//     * Test API Create
//     * @throws DBALException
//     * @throws DBALDriverException
//     */
//    public function testCreate()
//    {
//        $this->setPreventCommits();
//
//        $identifier = 'test_scope';
//        $name = 'Test scope';
//        $description = 'OAuth scope for testing';
//        $jsonInput = <<<EOT
//        {
//            "data" : {
//                "type" : "oauth-scopes",
//                "attributes" : {
//                    "identifier": "$identifier",
//                    "name"      : "$name",
//                    "description": "$description"
//                }
//            }
//        }
//EOT;
//        $headers = $this->getAdministratorOAuthHeader();
//
//        $response = $this->postJsonApi(self::API_URI, $jsonInput, $headers);
//        $this->assertEquals(201, $response->getStatusCode());
//
//        $json = json_decode((string)$response->getBody());
//        $this->assertObjectHasAttribute('data', $json);
//        $id = $json->data->id;
//
//        // check user exists
//        $this->assertEquals(200, $this->get(self::API_URI . "/$id", [], $headers)->getStatusCode());
//
//        // ... or make same check in the database
//        $query = $this->getCapturedConnection()->createQueryBuilder();
//        $statement = $query
//            ->select('*')
//            ->from(ModelInterface::TABLE_NAME)
//            ->where(ModelInterface::FIELD_ID . '=' . $query->createPositionalParameter($id))
//            ->execute();
//        $this->assertNotEmpty($statement->fetchAssociative());
//    }
//
//    /**
//     * Test API create invalid data
//     */
//    public function testCreateInvalidData()
//    {
//        $this->setPreventCommits();
//
//        $name = "Invalid Scope";
//        $jsonInput = <<<EOT
//        {
//            "data" : {
//                "type" : "oauth-scopes",
//                "attributes" : {
//                    "name": "$name"
//                }
//            }
//        }
//EOT;
//        /** @var ThrowableResponseInterface $response */
//        $this->assertInstanceOf(
//            ThrowableResponseInterface::class,
//            $response = $this->postJsonApi(self::API_URI, $jsonInput, $this->getAdministratorOAuthHeader())
//        );
//        /** @var JsonApiException $exception */
//        $this->assertInstanceOf(JsonApiException::class, $exception = $response->getThrowable());
//
//        $this->assertCount(1, $exception->getErrors());
//        $error1 = $exception->getErrors()->getArrayCopy()[0];
//        $this->assertEquals('The value is required.', $error1->getDetail());
//    }
//
//    /**
//     * Test API update
//     * @throws DBALException
//     * @throws DBALDriverException
//     */
//    public function testUpdate()
//    {
//        $this->setPreventCommits();
//
//        $oldId = 1;
//        $newIdentifier = 'test_scope';
//        $newName = 'Test scope';
//        $jsonInput = <<<EOT
//        {
//            "data" : {
//                "type" : "oauth-scopes",
//                "id"   : "$oldId",
//                "attributes" : {
//                    "identifier": "$newIdentifier",
//                    "name": "$newName"
//                }
//            }
//        }
//EOT;
//        $headers = $this->getAdministratorOAuthHeader();
//
//        $response = $this->patchJsonApi(self::API_URI . "/$oldId", $jsonInput, $headers);
//        $this->assertEquals(200, $response->getStatusCode());
//
//        $json = json_decode((string)$response->getBody());
//        $this->assertObjectHasAttribute('data', $json);
//        $this->assertEquals($oldId, $json->data->id);
//
//        // check user exists
//        $this->assertEquals(403, $this->get(self::API_URI . "/$oldId", [], $headers)->getStatusCode());
//
//        // ... or make same check in the database
//        $query = $this->getCapturedConnection()->createQueryBuilder();
//        $statement = $query
//            ->select('*')
//            ->from(ModelInterface::TABLE_NAME)
//            ->where(ModelInterface::FIELD_ID . '=' . $query->createPositionalParameter($oldId))
//            ->execute();
//        $this->assertNotEmpty($values = $statement->fetchAssociative());
//        $this->assertEquals($newIdentifier, $values[ModelInterface::FIELD_IDENTIFIER]);
//        $this->assertEquals($newName, $values[ModelInterface::FIELD_NAME]);
//        $this->assertNotEmpty($values[TimestampFields::FIELD_UPDATED_AT]);
//    }
//
//    /**
//     * Test User's API.
//     */
//    public function testUnauthorizedDenied()
//    {
//        // no token header
//
//        /** @var ThrowableResponseInterface $response */
//        $this->assertInstanceOf(
//            ThrowableResponseInterface::class,
//            $response = $this->get(self::API_URI)
//        );
//        /** @var JsonApiException $exception */
//        $this->assertInstanceOf(JsonApiException::class, $exception = $response->getThrowable());
//
//        $this->assertCount(1, $exception->getErrors());
//        $error = $exception->getErrors()->getArrayCopy()[0];
//        $this->assertEquals('You are not authorized for action `canViewOAuthScopes`.', $error->getDetail());
//    }
}
