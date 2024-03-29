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

use App\Application;
use Closure;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\ConnectionException;
use Laminas\HttpHandlerRunner\Emitter\EmitterInterface;
use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use Whoa\Application\Contracts\Cookie\CookieFunctionsInterface;
use Whoa\Application\Contracts\Csrf\CsrfTokenStorageInterface;
use Whoa\Application\Contracts\Session\SessionFunctionsInterface;
use Whoa\Common\Reflection\ClassIsTrait;
use Whoa\Contracts\Container\ContainerInterface;
use Whoa\Contracts\Core\ApplicationInterface;
use Whoa\Flute\Contracts\Api\CrudInterface;
use Whoa\Flute\Contracts\FactoryInterface;
use Whoa\Testing\ApplicationWrapperInterface;
use Whoa\Testing\ApplicationWrapperTrait;
use Whoa\Testing\HttpCallsTrait;
use Whoa\Testing\MeasureExecutionTimeTrait;
use Whoa\Testing\Sapi;
use Whoa\Testing\TestCaseTrait;
use LogicException;
use Mockery;
use Psr\Container\ContainerInterface as PsrContainerInterface;

/**
 * @package Tests
 */
class TestCase extends \PHPUnit\Framework\TestCase
{
    use ClassIsTrait;
    use HttpCallsTrait;
    use MeasureExecutionTimeTrait;
    use OAuthSignInTrait;
    use TestCaseTrait;

    /** @var bool */
    private bool $shouldPreventCommits = false;

    /** @var bool */
    private bool $isInTransaction = false;

    /**
     * Database connection shared during test when commit prevention is requested.
     *
     * @var Connection|null
     */
    private ?Connection $sharedConnection = null;

    /**
     * Next call replacements in the application container.
     *
     * @var array
     */
    private array $containerToReplace = [];

    /**
     * Next call captures from the application container.
     *
     * @var array
     */
    private array $containerToCapture = [];

    /**
     * Captured from container on previous application call.
     *
     * @var array
     */
    private array $containerCaptured = [];

    /**
     * @var Closure[]
     */
    private array $containerModifiers = [];

    /**
     * @inheritdoc
     */
    protected function setUp()
    {
        parent::setUp();

        $this->resetEventHandlers();

        // keep database connection between multiple App call during a single test
        $this->sharedConnection = null;
        $this->shouldPreventCommits = false;
        $interceptConnection = function (ApplicationInterface $app, ContainerInterface $container) {
            assert($app);
            if ($this->shouldPreventCommits === false) {
                // just capture connection from the call / typically is captured before test starts
                $this->sharedConnection = $container->get(Connection::class);
            } else {
                // we are here if `prevent commits` is activated in a test

                if ($this->isInTransaction === false) {
                    $this->sharedConnection->beginTransaction();
                    $this->isInTransaction = true;
                }
                // we always have same connection during a single test case if `prevent commits` is activated
                // the code below expects that app is created and connection is captured before test in `setUp()`
                $container[Connection::class] = $this->sharedConnection;
            }
        };
        $this->addOnContainerConfiguredEvent($interceptConnection);

        // in testing environment replace PHP session & cookie functions with mocks
        $replaceSessionFunctions = function (ApplicationInterface $app, ContainerInterface $container) {
            assert($app);
            $doNothing = function () {
            };
            if ($container->has(SessionFunctionsInterface::class) === true) {
                // session values could be retrieved by capturing session interface before app call.
                $sessionValues = [];
                /** @var SessionFunctionsInterface $functions */
                $functions = $container->get(SessionFunctionsInterface::class);
                $functions
                    ->setStartCallable($doNothing)
                    ->setWriteCloseCallable($doNothing)
                    ->setHasCallable(function ($key) use (&$sessionValues): bool {
                        return array_key_exists($key, $sessionValues);
                    })
                    ->setPutCallable(function ($key, $value) use (&$sessionValues): void {
                        $sessionValues[$key] = $value;
                    })
                    ->setRetrieveCallable(function ($key) use (&$sessionValues) {
                        return $sessionValues[$key];
                    });
            }

            if ($container->has(CookieFunctionsInterface::class) === true) {
                /** @var CookieFunctionsInterface $functions */
                $functions = $container->get(CookieFunctionsInterface::class);
                $functions
                    ->setWriteCookieCallable($doNothing)
                    ->setWriteRawCookieCallable($doNothing);
            }
        };
        $this->addOnContainerConfiguredEvent($replaceSessionFunctions);

        $this->addOnContainerConfiguredEvent(function (ApplicationInterface $app, ContainerInterface $container) {
            assert($app);

            foreach ($this->getContainerToReplace() as $interface => $value) {
                $container->offsetSet(
                    $interface,
                    is_callable($value) === true ? call_user_func($value, $app, $container) : $value
                );
            }
            $this->clearToReplace();

            $this->clearCaptured();
            foreach ($this->getContainerToCapture() as $interface) {
                if ($container->has($interface) === false) {
                    throw new LogicException("Application container do not contain any value with `$interface` key.");
                }
                $this->rememberCaptured($interface, $container->get($interface));
            }
            $this->clearToCapture();

            foreach ($this->getContainerModifiers() as $modifierClosure) {
                assert($modifierClosure instanceof Closure);
                call_user_func($modifierClosure, $app, $container);
            }
            $this->clearContainerModifiers();
        });

        // create app which calls event handlers above
        $this->createApplication()->createContainer();
    }

    /**
     * @inheritdoc
     * @throws ConnectionException
     */
    protected function tearDown()
    {
        parent::tearDown();

        if ($this->shouldPreventCommits === true &&
            $this->sharedConnection !== null &&
            $this->isInTransaction === true
        ) {
            $this->sharedConnection->rollBack();
        }
        $this->sharedConnection = null;
        $this->shouldPreventCommits = false;
        $this->isInTransaction = false;
        $this->resetEventHandlers();
        $this->clearContainerModifiers();
        $this->clearToCapture()->clearCaptured()->clearToReplace();

        Mockery::close();
    }

    /**
     * Prevent commits to database within current test.
     * @return void
     */
    protected function setPreventCommits()
    {
        $this->shouldPreventCommits = true;
    }

    /**
     * Returns database connection used by application within current test.
     * @return Connection
     */
    protected function getCapturedConnection(): ?Connection
    {
        return $this->sharedConnection;
    }

    /**
     * @inheritdoc
     */
    protected function createApplication(): ApplicationInterface
    {
        $wrapper = new class extends Application implements ApplicationWrapperInterface {
            use ApplicationWrapperTrait;
        };

        foreach ($this->getHandleRequestEvents() as $handler) {
            $wrapper->addOnHandleRequest($handler);
        }

        foreach ($this->getHandleResponseEvents() as $handler) {
            $wrapper->addOnHandleResponse($handler);
        }

        foreach ($this->getContainerCreatedEvents() as $handler) {
            $wrapper->addOnContainerCreated($handler);
        }

        foreach ($this->getContainerConfiguredEvents() as $handler) {
            $wrapper->addOnContainerLastConfigurator($handler);
        }

        return $wrapper;
    }

    /**
     * @inheritdoc
     */
    protected function createSapi(
        array $server = null,
        array $queryParams = null,
        array $parsedBody = null,
        array $cookies = null,
        array $files = null,
        $messageBody = 'php://input',
        string $protocolVersion = '1.1'
    ): Sapi {
        /** @var EmitterInterface $emitter */
        $emitter = Mockery::mock(EmitterInterface::class);

        return new Sapi($emitter, $server, $queryParams, $parsedBody, $cookies, $files, $messageBody, $protocolVersion);
    }

    /**
     * @return self
     */
    protected function passThroughCsrfOnNextAppCall(): self
    {
        $csrfMock = Mockery::mock(CsrfTokenStorageInterface::class);
        $csrfMock->shouldReceive('check')->once()->withAnyArgs()->andReturn(true);

        $this->replaceInNextAppCall(CsrfTokenStorageInterface::class, $csrfMock);

        return $this;
    }

    /**
     * @param string $interface
     * @param callable|mixed $value
     * @return TestCase
     */
    protected function replaceInNextAppCall(string $interface, $value): self
    {
        assert(array_key_exists($interface, $this->containerToReplace) === false);

        $this->containerToReplace[$interface] = $value;

        return $this;
    }

    /**
     * @param string $interface
     * @return TestCase
     */
    protected function captureFromNextAppCall(string $interface): self
    {
        assert(in_array($interface, $this->containerToCapture) === false);

        $this->containerToCapture[] = $interface;

        return $this;
    }

    /**
     * @param string $interface
     * @return mixed
     */
    protected function getCapturedFromPreviousAppCall(string $interface)
    {
        if (array_key_exists($interface, $this->containerCaptured) === false) {
            throw new LogicException(
                "Nothing was captured by name `$interface`. " .
                'Have you forgotten to call `capture` method before the application call?'
            );
        }

        return $this->containerCaptured[$interface];
    }

    /**
     * @return array
     */
    protected function getContainerCaptured(): array
    {
        return $this->containerCaptured;
    }

    /**
     * @return self
     */
    protected function setAdministrator(): self
    {
        return $this->addNextCallContainerModifier(
            $this->createSetUserClosureWithCredentials(
                $this->getAdministratorEmail(),
                $this->getAdministratorPassword()
            )
        );
    }

    /**
     * @return self
     */
    protected function setModerator(): self
    {
        return $this->addNextCallContainerModifier(
            $this->createSetUserClosureWithCredentials($this->getModeratorEmail(), $this->getModeratorPassword())
        );
    }

    /**
     * @return self
     */
    protected function setUser(): self
    {
        return $this->addNextCallContainerModifier(
            $this->createSetUserClosureWithCredentials($this->getUserEmail(), $this->getUserPassword())
        );
    }

    /**
     * @param string $accessToken
     * @return self
     */
    protected function setUserByToken(string $accessToken): self
    {
        return $this->addNextCallContainerModifier(
            $this->createSetUserClosureWithAccessToken($accessToken)
        );
    }

    /**
     * @param string $apiClass
     * @param PsrContainerInterface|null $container
     * @return CrudInterface
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     */
    protected function createApi(string $apiClass, PsrContainerInterface $container = null): CrudInterface
    {
        assert(
            $this->classImplements(
                $apiClass,
                CrudInterface::class
            ),
            "Class `$apiClass` does not look like a valid API CRUD class."
        );

        if ($container === null) {
            $container = $this->createApplication()->createContainer();
        }

        /** @var FactoryInterface $factory */
        $factory = $container->get(FactoryInterface::class);
        return $factory->createApi($apiClass);
    }

    /**
     * @return array
     */
    private function getContainerToCapture(): array
    {
        return $this->containerToCapture;
    }

    /**
     * @return array
     */
    private function getContainerToReplace(): array
    {
        return $this->containerToReplace;
    }

    /**
     * @return self
     */
    private function clearToReplace(): self
    {
        $this->containerToReplace = [];

        return $this;
    }

    /**
     * @return self
     */
    private function clearToCapture(): self
    {
        $this->containerToCapture = [];

        return $this;
    }

    /**
     * @return self
     */
    private function clearCaptured(): self
    {
        $this->containerCaptured = [];

        return $this;
    }

    /**
     * @param string $interface
     * @param mixed $value
     */
    private function rememberCaptured(string $interface, $value): void
    {
        assert(array_key_exists($interface, $this->containerCaptured) === false);

        $this->containerCaptured[$interface] = $value;
    }

    /**
     * @return Closure[]
     */
    private function getContainerModifiers(): array
    {
        return $this->containerModifiers;
    }

    /**
     * @return self
     */
    private function clearContainerModifiers(): self
    {
        $this->containerModifiers = [];

        return $this;
    }

    /**
     * @param Closure $modifier
     * @return TestCase
     */
    protected function addNextCallContainerModifier(Closure $modifier): self
    {
        $this->containerModifiers[] = $modifier;

        return $this;
    }
}
