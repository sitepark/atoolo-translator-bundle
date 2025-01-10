<?php

declare(strict_types=1);

namespace Atoolo\Translator\Test\Console\Command;

use Atoolo\Translator\Console\Application;
use Atoolo\Translator\Console\Command\CacheGet;
use Atoolo\Translator\Service\TextHasher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;

#[CoversClass(CacheGet::class)]
class CacheGetTest extends TestCase
{
    private CommandTester $commandTester;

    private CacheItemPoolInterface $translationCache;

    /**
     * @throws Exception
     */
    public function setUp(): void
    {
        $this->translationCache = $this->createStub(CacheItemPoolInterface::class);
        $textHasher = $this->createStub(TextHasher::class);

        $cacheGet = new CacheGet($this->translationCache, $textHasher);
        $application = new Application([
            $cacheGet,
        ]);

        $command = $application->get('translator:cache:get');
        $this->commandTester = new CommandTester($command);
    }

    /**
     * @throws Exception
     */
    public function testNotFound(): void
    {

        $this->translationCache->method('hasItem')->willReturn(false);

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
No translation found in cache.
EOF,
            trim($output),
        );
    }

    /**
     * @throws Exception
     */
    public function testFoundNoHit(): void
    {

        $this->translationCache->method('hasItem')->willReturn(true);
        $item = $this->createStub(CacheItemInterface::class);
        $item->method('isHit')->willReturn(false);
        $this->translationCache->method('getItem')->willReturn($item);

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
No translation found in cache.
EOF,
            trim($output),
        );
    }


    /**
     * @throws Exception
     */
    public function testFound(): void
    {

        $this->translationCache->method('hasItem')->willReturn(true);
        $item = $this->createStub(CacheItemInterface::class);
        $item->method('isHit')->willReturn(true);
        $item->method('get')->willReturn('Hallo Welt');
        $this->translationCache->method('getItem')->willReturn($item);

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
