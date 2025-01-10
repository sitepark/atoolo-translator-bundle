<?php

declare(strict_types=1);

namespace Atoolo\Translator\Test\Console\Command\Io;

use Atoolo\Translator\Console\Command\Io\TypifiedInput;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Input\InputInterface;

#[CoversClass(TypifiedInput::class)]
class TypifiedInputTest extends TestCase
{
    public function testGetStringOption(): void
    {
        $symfonyInput = $this->createStub(InputInterface::class);
        $symfonyInput
            ->method('getOption')
            ->willReturn('abc');

        $input = new TypifiedInput($symfonyInput);

        $this->assertEquals(
            'abc',
            $input->getStringOption('a'),
            'unexpected option value',
        );
    }

    /**
     * @throws Exception
     */
    public function testGetStringOptWithInvalidValue(): void
    {
        $symfonyInput = $this->createStub(InputInterface::class);
        $symfonyInput
            ->method('getOption')
            ->willReturn(123);

        $input = new TypifiedInput($symfonyInput);

        $this->expectException(InvalidArgumentException::class);
        $input->getStringOption('a');
    }

    /**
     * @throws Exception
     */
    public function testGetStringArgument(): void
    {
        $symfonyInput = $this->createStub(InputInterface::class);
        $symfonyInput
            ->method('getArgument')
            ->willReturn('abc');

        $input = new TypifiedInput($symfonyInput);

        $this->assertEquals(
            'abc',
            $input->getStringArgument('a'),
            'unexpected argument value',
        );
    }

    /**
     * @throws Exception
     */
    public function testGetStringArgumentWithInvalidValue(): void
    {
        $symfonyInput = $this->createStub(InputInterface::class);
        $symfonyInput
            ->method('getArgument')
            ->willReturn(123);

        $input = new TypifiedInput($symfonyInput);

        $this->expectException(InvalidArgumentException::class);
        $input->getStringArgument('a');
    }

}
