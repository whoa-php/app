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

namespace Tests\Cli;

use Psr\Container\ContainerExceptionInterface;
use Psr\Container\NotFoundExceptionInterface;
use ReflectionException;
use Whoa\Application\Commands\DataCommand;
use Whoa\Commands\ExecuteCommandTrait;
use Whoa\Contracts\Commands\CommandInterface;
use Whoa\Testing\CommandsDebugIo;
use Tests\TestCase;

/**
 * @package Tests
 */
class CliiTest extends TestCase
{
    use ExecuteCommandTrait;

    /**
     * Demo how you can test and debug console commands.
     * In this example one of the standard command is executed,
     * however custom commands could be tested the same way.
     * @throws ContainerExceptionInterface
     * @throws NotFoundExceptionInterface
     * @throws ReflectionException
     */
    public function testCommand(): void
    {
        $this->setPreventCommits();

        $arguments = [DataCommand::ARG_ACTION => DataCommand::ACTION_SEED];
        $options = [];
        $ioMock = new CommandsDebugIo($arguments, $options);

        $container = $this->createApplication()->createContainer();

        $this->executeCommand(
            DataCommand::NAME,
            [DataCommand::class, CommandInterface::COMMAND_METHOD_NAME],
            $ioMock,
            $container
        );

        $this->assertEmpty($ioMock->getErrorRecords());
        $this->assertEmpty($ioMock->getWarningRecords());
        $this->assertNotEmpty($ioMock->getInfoRecords());
    }
}
