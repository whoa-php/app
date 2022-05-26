<?php

declare(strict_types=1);

namespace Tests\Json;

use App\Data\Models\User as Model;
use App\Data\Seeds\RolesSeed;
use App\Data\Seeds\UsersSeed as Seed;
use App\Json\Schemas\UserSchema as Schema;
use Doctrine\DBAL\Driver\Exception as DBALDriverException;
use Doctrine\DBAL\Exception as DBALException;
use Whoa\Contracts\Data\TimestampFields;
use Whoa\Contracts\Http\ThrowableResponseInterface;
use Whoa\Testing\JsonApiCallsTrait;
use Neomerx\JsonApi\Exceptions\JsonApiException;
use Tests\TestCase;

/**
 * @package Tests
 */
class UserApiTest extends TestCase
{
    use JsonApiCallsTrait;

    public const API_URI = '/api/v1/' . Schema::TYPE;

    /**
     * Test User's API.
     */
    public function testIndex()
    {
        $this->setPreventCommits();

        $response = $this->get(self::API_URI, [], $this->getAdministratorOAuthHeader());
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertCount(3, $json->data);
    }

    /**
     * Test index with parameters.
     */
    public function testIndexWithInclude()
    {
        $this->setPreventCommits();

        $queryParams = [
            'filter' => [
                'id' => [
                    'greater-than' => '1',  // 'long' form for condition operations
                    'lte' => '5',  // 'short' form supported as well
                ],
                'role.name' => [
                    'like' => '%',          // example how conditions could be applied to relationships' attributes
                ],
            ],
            'sort' => '+id,email', // example of how multiple sorting conditions could be applied
            'include' => 'role',
            'fields' => [
                'users' => 'id,email,role',
            ],
        ];

        $headers = $this->getAdministratorOAuthHeader();
        $response = $this->get(self::API_URI, $queryParams, $headers);

        $this->assertEquals(200, $response->getStatusCode());
        $this->assertNotNull($resources = json_decode((string)$response->getBody()));
        $this->assertCount(2, $resources->data);

        $resource = $resources->data[0];
        $this->assertEquals(2, $resource->id);
        $this->assertEquals(RolesSeed::ID_MODERATORS, $resource->relationships->role->data->id);

        $resource = $resources->data[1];
        $this->assertEquals(3, $resource->id);
        $this->assertEquals(RolesSeed::ID_USERS, $resource->relationships->role->data->id);

        // check response has included posts as well
        $this->assertCount(2, $resources->included);
    }

    /**
     * Test User's API.
     */
    public function testIndexInvalidToken()
    {
        $response = $this->get(self::API_URI, [], $this->getOAuthHeader('XXX'));
        $this->assertEquals(401, $response->getStatusCode());
    }

    /**
     * Test User's API.
     */
    public function testRead()
    {
        $this->setPreventCommits();

        $userId = '1';
        $response = $this->get(self::API_URI . "/$userId", [], $this->getAdministratorOAuthHeader());
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertEquals($userId, $json->data->id);
        $this->assertEquals(Schema::TYPE, $json->data->type);
    }

    /**
     * Test User's API.
     */
    public function testDelete()
    {
        $this->setPreventCommits();

        $userId = '2';
        $headers = $this->getAdministratorOAuthHeader();

        // check user exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$userId", [], $headers)->getStatusCode());

        // delete
        $this->assertEquals(204, $this->delete(self::API_URI . "/$userId", [], $headers)->getStatusCode());

        // check user do not exist (catch-all route returning 200)
        $this->assertEquals(200, $this->get(self::API_URI . "/$userId", [], $headers)->getStatusCode());
    }

    /**
     * Test User's API.
     * @throws DBALException
     * @throws DBALDriverException
     */
    public function testCreate()
    {
        $this->setPreventCommits();

        $roleId = RolesSeed::ID_ADMINISTRATORS;
        $password = 'secret';
        $email = "john@dow.foo";
        $jsonInput = <<<EOT
        {
            "data" : {
                "type" : "users",
                "attributes" : {
                    "email"      : "$email",
                    "password"   : "$password"
                },
                "relationships": {
                    "role": {
                        "data": { "type": "roles", "id": "$roleId" }
                    }
                }
            }
        }
EOT;
        $headers = $this->getAdministratorOAuthHeader();

        $response = $this->postJsonApi(self::API_URI, $jsonInput, $headers);
        $this->assertEquals(201, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $userId = $json->data->id;

        // check user exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$userId", [], $headers)->getStatusCode());

        // ... or make same check in the database
        $query = $this->getCapturedConnection()->createQueryBuilder();
        $statement = $query
            ->select('*')
            ->from(Model::TABLE_NAME)
            ->where(Model::FIELD_ID . '=' . $query->createPositionalParameter($userId))
            ->execute();
        $this->assertNotEmpty($statement->fetchAssociative());
    }

    /**
     * Test User's API.
     */
    public function testCreateInvalidData()
    {
        $this->setPreventCommits();

        $password = 'secret';
        $email = "it_does_not_look_like_an_email";
        $jsonInput = <<<EOT
        {
            "data" : {
                "type" : "users",
                "attributes" : {
                    "first-name" : "John",
                    "last-name"  : "Dow",
                    "email"      : "$email",
                    "password"   : "$password"
                },
                "relationships": {
                    "role": {
                        "data": { "type": "roles", "id": "user" }
                    }
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

        $this->assertCount(3, $exception->getErrors());
        $error1 = $exception->getErrors()->getArrayCopy()[0];
        $error2 = $exception->getErrors()->getArrayCopy()[1];
        $error3 = $exception->getErrors()->getArrayCopy()[2];
        $this->assertEquals('Unknown JSON API attribute.', $error1->getDetail());
        $this->assertEquals('Unknown JSON API attribute.', $error2->getDetail());
        $this->assertEquals('The value should be an integer.', $error3->getDetail());
    }

    /**
     * Test User's API.
     * @throws DBALDriverException
     * @throws DBALException
     */
    public function testUpdate()
    {
        $this->setPreventCommits();

        $roleId = RolesSeed::ID_ADMINISTRATORS;
        $userId = 2;
        $jsonInput = <<<EOT
        {
            "data" : {
                "type" : "users",
                "id"   : "$userId",
                "attributes" : {
                    "password"   : "new-secret"
                },
                "relationships": {
                    "role": {
                        "data": { "type": "roles", "id": "$roleId" }
                    }
                }
            }
        }
EOT;
        $headers = $this->getAdministratorOAuthHeader();

        $response = $this->patchJsonApi(self::API_URI . "/$userId", $jsonInput, $headers);
        $this->assertEquals(200, $response->getStatusCode());

        $json = json_decode((string)$response->getBody());
        $this->assertObjectHasAttribute('data', $json);
        $this->assertEquals($userId, $json->data->id);

        // check user exists
        $this->assertEquals(200, $this->get(self::API_URI . "/$userId", [], $headers)->getStatusCode());

        // ... or make same check in the database
        $query = $this->getCapturedConnection()->createQueryBuilder();
        $statement = $query
            ->select('*')
            ->from(Model::TABLE_NAME)
            ->where(Model::FIELD_ID . '=' . $query->createPositionalParameter($userId))
            ->execute();
        $this->assertNotEmpty($values = $statement->fetchAssociative());
        $this->assertEquals(Seed::EMAIL_DEFAULT_MODERATOR, $values[Model::FIELD_EMAIL]);
        $this->assertNotEmpty($values[TimestampFields::FIELD_UPDATED_AT]);
        $this->assertEquals(RolesSeed::ID_ADMINISTRATORS, $values[Model::FIELD_ID_ROLE]);
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
        $this->assertEquals('You are not authorized for action `canViewUsers`.', $error->getDetail());
    }
}
