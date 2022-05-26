<?php

declare(strict_types=1);

namespace Tests\Cli;

use Exception;
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
     *
     * In this example one of the standard command is executed,
     * however custom commands could be tested the same way.
     *
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
