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

namespace App\AzureClient;

use App\Api\UsersApi;
use Doctrine\DBAL\Driver\Exception;
use Doctrine\DBAL\Exception as DBALException;
use Laminas\Diactoros\Response\EmptyResponse;
use Laminas\Diactoros\Response\JsonResponse;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;
use Whoa\Flute\Contracts\FactoryInterface;
use Whoa\OAuthClient\Contracts\JsonWebToken\Azure\Version2\AzureJwtIdentityInterface;
use Whoa\OAuthClient\Exceptions\InvalidArgumentException;
use Whoa\OAuthClient\Exceptions\RuntimeException;
use Whoa\Passport\Contracts\Entities\TokenInterface;
use Whoa\Passport\Contracts\PassportServerIntegrationInterface;

/**
 * @package App
 */
class AzureClientController
{
    /** @var callable Token exchange handler */
    public const TOKEN_HANDLER = [self::class, 'token'];

    /**
     * @param array $routeParams
     * @param ContainerInterface $container
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    public static function token(
        array $routeParams,
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        assert(empty($routeParams) === true);
        return self::postCreateToken($container, $request);
    }

    /**
     * @param ContainerInterface $container
     * @param ServerRequestInterface $request
     * @return ResponseInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected static function postCreateToken(
        ContainerInterface $container,
        ServerRequestInterface $request
    ): ResponseInterface {
        try {
            if (array_key_exists('idToken', ($parameters = $request->getParsedBody())) === false) {
                throw new AzureClientException(AzureClientException::ERROR_MISSING_ID_TOKEN);
            }

            /** @var AzureClientInterface $azureClient */
            /** @var PassportServerIntegrationInterface $passport */
            /** @var FactoryInterface $apiFactory */
            /** @var UsersApi $usersApi */
            $azureClient = $container->get(AzureClientInterface::class);
            $passport = $container->get(PassportServerIntegrationInterface::class);
            $apiFactory = $container->get(FactoryInterface::class);
            $usersApi = $apiFactory->createApi(UsersApi::class);

            $jwtIdentities = $azureClient->setSerializeJwt($parameters['idToken'])->getJwtIdentities();

            if (($userId = $passport->validateUserId(
                    $jwtIdentities[AzureJwtIdentityInterface::KEY_USERNAME]
                )) === null) {
                if ($usersApi->noAuthReadUserIdByEmail(
                        $jwtIdentities[AzureJwtIdentityInterface::KEY_USERNAME]
                    ) === null) {
                    $userId = $usersApi->noAuthCreateUser($jwtIdentities[AzureJwtIdentityInterface::KEY_USERNAME]);
                } else {
                    throw new AzureClientException(AzureClientException::ERROR_LOG_IN);
                }
            }

            [$savedToken, $tokenExpiresIn] = static::generateToken($passport, (string)$userId, $jwtIdentities);

            return static::createBodyTokenResponse($passport, $savedToken, $tokenExpiresIn);
        } catch (AzureClientException|RuntimeException|InvalidArgumentException|Exception|DBALException|NotFoundExceptionInterface|ContainerExceptionInterface $exception) {
            return self::createBodyErrorResponse($exception);
        }
    }

    /**
     * @param PassportServerIntegrationInterface $passport
     * @param string $userId
     * @param array $jwtIdentities
     * @return array
     */
    private static function generateToken(
        PassportServerIntegrationInterface $passport,
        string $userId,
        array $jwtIdentities
    ): array {
        $clientScopes = $passport->getClientRepository()
            ->readScopeIdentifiers($jwtIdentities[AzureJwtIdentityInterface::KEY_PROVIDER_IDENTIFIER]);

        $changedScopeOrNull = $passport->verifyAllowedUserScope((int)$userId, $clientScopes);

        $unsavedToken = $passport
            ->createTokenInstance()
            ->setClientIdentifier($jwtIdentities[AzureJwtIdentityInterface::KEY_PROVIDER_IDENTIFIER])
            ->setUserIdentifier($userId);

        if ($changedScopeOrNull === null) {
            // here will be users with scopes identical to client's ones aka unlimited (e.g. admins)
            $unsavedToken->setScopeIdentifiers($clientScopes)->setScopeUnmodified();
        } else {
            // here will be less privileged users with scope less than client's default
            $unsavedToken->setScopeIdentifiers($changedScopeOrNull)->setScopeModified();
        }

        [$tokenValue, $tokenType, $tokenExpiresIn, $refreshValue] = $passport->generateTokenValues($unsavedToken);

        $unsavedToken->setValue($tokenValue)->setType($tokenType)->setRefreshValue($refreshValue);
        $savedToken = $passport->getTokenRepository()->createToken($unsavedToken);

        return [$savedToken, $tokenExpiresIn];
    }

    /**
     * @param $exception
     * @return ResponseInterface
     */
    private static function createBodyErrorResponse($exception): ResponseInterface
    {
        $data = array_filter([
            'error' => $exception->getErrorCode(),
            'error_description' => $exception->getErrorDescription(),
            'error_uri' => $exception->getErrorUri() != null ? $exception : null,
        ]);

        return new JsonResponse($data, $exception->getHttpCode(), $exception->getHttpHeaders());
    }

    /**
     * @param PassportServerIntegrationInterface $passport
     * @param TokenInterface $token
     * @param int $tokenExpiresIn
     * @return ResponseInterface
     */
    private static function createBodyTokenResponse(
        PassportServerIntegrationInterface $passport,
        TokenInterface $token,
        int $tokenExpiresIn
    ): ResponseInterface {
        $scopeList = $token->isScopeModified() === false || empty($token->getScopeIdentifiers()) === true ?
            null : $token->getScopeList();

        // for access token format @link https://tools.ietf.org/html/rfc6749#section-5.1
        $parameters = array_filter([
            'access_token' => $token->getValue(),
            'token_type' => $token->getType(),
            'expires_in' => $tokenExpiresIn,
            'refresh_token' => $token->getRefreshValue(),
            'scope' => $scopeList,
        ]);

        // extra parameters
        // https://tools.ietf.org/html/rfc6749#section-4.1.4
        // https://tools.ietf.org/html/rfc6749#section-4.3.3
        // https://tools.ietf.org/html/rfc6749#section-4.4.3
        $extraParameters = $passport->getBodyTokenExtraParameters($token);

        return new JsonResponse($parameters + $extraParameters, 200, [
            'Cache-Control' => 'no-store',
            'Pragma' => 'no-cache'
        ]);
    }
}
