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

namespace Tests;

use App\Data\Seeds\UsersSeed;
use App\Web\Middleware\CookieAuth;
use Closure;
use Laminas\Diactoros\ServerRequest;
use Whoa\Contracts\Core\ApplicationInterface;
use Whoa\Contracts\Passport\PassportAccountManagerInterface;
use Whoa\Passport\Contracts\PassportServerInterface;
use Psr\Container\ContainerInterface;
use Psr\Http\Message\ResponseInterface;

/**
 * @package Tests
 *
 * @method ResponseInterface post(string $url, array $data)
 */
trait OAuthSignInTrait
{
    /**
     * @return string
     */
    private function getUserEmail(): string
    {
        return UsersSeed::EMAIL_DEFAULT_USER;
    }

    /**
     * @return string
     */
    private function getUserPassword(): string
    {
        return UsersSeed::DEFAULT_PASSWORD;
    }

    /**
     *
     * @return string
     */
    private function getModeratorEmail(): string
    {
        return UsersSeed::EMAIL_DEFAULT_MODERATOR;
    }

    /**
     * @return string
     */
    private function getModeratorPassword(): string
    {
        return UsersSeed::DEFAULT_PASSWORD;
    }

    /**
     * @return string
     */
    private function getAdministratorEmail(): string
    {
        return UsersSeed::EMAIL_DEFAULT_ADMINISTRATOR;
    }

    /**
     * @return string
     */
    private function getAdministratorPassword(): string
    {
        return UsersSeed::DEFAULT_PASSWORD;
    }

    /**
     * @return array
     */
    protected function getAdministratorOAuthHeader(): array
    {
        return $this->getOAuthHeader($this->extractOAuthAccessTokenValue($this->getAdministratorOAuthToken()));
    }

    /**
     * @return array
     */
    protected function getModeratorOAuthHeader(): array
    {
        return $this->getOAuthHeader($this->extractOAuthAccessTokenValue($this->getModeratorOAuthToken()));
    }

    /**
     * @return array
     */
    protected function getUserOAuthHeader(): array
    {
        return $this->getOAuthHeader($this->extractOAuthAccessTokenValue($this->getUserOAuthToken()));
    }

    /**
     * @return array
     */
    protected function getAdministratorOAuthCookie(): array
    {
        return $this->getOAuthCookie($this->extractOAuthAccessTokenValue($this->getAdministratorOAuthToken()));
    }

    /**
     * @return array
     */
    protected function getModeratorOAuthCookie(): array
    {
        return $this->getOAuthCookie($this->extractOAuthAccessTokenValue($this->getModeratorOAuthToken()));
    }

    /**
     * @return array
     */
    protected function getUserOAuthCookie(): array
    {
        return $this->getOAuthCookie($this->extractOAuthAccessTokenValue($this->getUserOAuthToken()));
    }

    /**
     * @param string $token
     *
     * @return array
     */
    protected function getOAuthHeader(string $token): array
    {
        return ['Authorization' => 'Bearer ' . $token];
    }

    /**
     * @param string $token
     *
     * @return array
     */
    protected function getOAuthCookie(string $token): array
    {
        return [CookieAuth::COOKIE_NAME => $token];
    }

    /**
     * @return object
     */
    protected function getAdministratorOAuthToken(): object
    {
        return $this->getOAuthToken($this->getAdministratorEmail(), $this->getAdministratorPassword());
    }

    /**
     * @return object
     */
    protected function getModeratorOAuthToken(): object
    {
        return $this->getOAuthToken($this->getModeratorEmail(), $this->getModeratorPassword());
    }

    /**
     * @return object
     */
    protected function getUserOAuthToken(): object
    {
        return $this->getOAuthToken($this->getUserEmail(), $this->getUserPassword());
    }

    /**
     * @param string $username
     * @param string $password
     * @return object
     */
    protected function getOAuthToken(string $username, string $password): object
    {
        $response = $this->post('/token', $this->createOAuthTokenRequestBody($username, $password));

        assert($response->getStatusCode() == 200);
        $token = json_decode((string)$response->getBody());
        assert($token !== false);

        return $token;
    }

    /**
     * @param object $token
     * @return string
     */
    private function extractOAuthAccessTokenValue($token): string
    {
        assert(is_object($token));
        assert(isset($token->access_token));
        $value = $token->access_token;
        assert(empty($value) === false);

        return $value;
    }

    /**
     * @param string $username
     * @param string $password
     * @return Closure
     */
    private function createSetUserClosureWithCredentials(string $username, string $password): Closure
    {
        return function (ApplicationInterface $app, ContainerInterface $container) use ($username, $password): void {
            assert($app !== null);

            $request = (new ServerRequest())->withParsedBody($this->createOAuthTokenRequestBody($username, $password));

            /** @var PassportServerInterface $passportServer */
            $passportServer = $container->get(PassportServerInterface::class);
            $tokenResponse = $passportServer->postCreateToken($request);
            assert($tokenResponse->getStatusCode() === 200);
            $token = json_decode((string)$tokenResponse->getBody());
            $authToken = $token->access_token;

            /** @var PassportAccountManagerInterface $manager */
            assert($container->has(PassportAccountManagerInterface::class));
            $manager = $container->get(PassportAccountManagerInterface::class);
            $manager->setAccountWithTokenValue($authToken);
        };
    }

    /**
     * @param string $accessToken
     * @return Closure
     */
    private function createSetUserClosureWithAccessToken(string $accessToken): Closure
    {
        return function (ApplicationInterface $app, ContainerInterface $container) use ($accessToken): void {
            assert($app !== null);

            /** @var PassportAccountManagerInterface $manager */
            assert($container->has(PassportAccountManagerInterface::class));
            $manager = $container->get(PassportAccountManagerInterface::class);
            $manager->setAccountWithTokenValue($accessToken);
        };
    }

    /**
     * @param string $username
     * @param string $password
     * @return array
     */
    private function createOAuthTokenRequestBody(string $username, string $password): array
    {
        return [
            'grant_type' => 'password',
            'username' => $username,
            'password' => $password,
        ];
    }
}
