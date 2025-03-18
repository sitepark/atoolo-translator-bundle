<?php

declare(strict_types=1);

namespace Atoolo\Translator\Test\Console\Command;

use Atoolo\Translator\Console\Application;
use Atoolo\Translator\Console\Command\Translate;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

#[CoversClass(Translate::class)]
class TranslateTest extends TestCase
{
    private CommandTester $commandTester;

    private \Atoolo\Translator\Service\Translator $translator;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->translator = $this->createStub(\Atoolo\Translator\Service\Translator::class);

        $translator = new Translate($this->translator);
        $application = new Application([
            $translator,
        ]);

        $command = $application->get('translator:translate');
        $this->commandTester = new CommandTester($command);
    }

    /**
     * @throws Exception
     */
    public function testNotFound(): void
    {

        $this->translator->method('translate')->willReturn(['Hallo Welt']);

        $this->commandTester->execute([
            '-l' => 'de',
            'text' => 'Hello World',
        ]);

        $this->assertEquals(
            Command::SUCCESS,
            $this->commandTester->getStatusCode(),
            'command should return success',
        );

        $output = $this->commandTester->getDisplay();
        $this->assertEquals(
            <<<EOF
Hallo Welt
EOF,
            trim($output),
        );
    }
}
