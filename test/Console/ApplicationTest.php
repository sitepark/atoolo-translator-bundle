<?php

declare(strict_types=1);

namespace Atoolo\Translator\Test\Console;

use Atoolo\Translator\Console\Application;
use Atoolo\Translator\Console\Command\CacheGet;
use Atoolo\Translator\Service\TextHasher;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\Exception;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemPoolInterface;

#[CoversClass(Application::class)]
class ApplicationTest extends TestCase
{
    /**
     * @throws Exception
     */
    public function testConstruct(): void
    {
        $translationCache = $this->createStub(CacheItemPoolInterface::class);
        $textHasher = $this->createStub(TextHasher::class);

        $cacheGet = new CacheGet($translationCache, $textHasher);
        $application = new Application([
            $cacheGet,
        ]);

        $command = $application->get('translator:cache:get');
        $this->assertInstanceOf(
            CacheGet::class,
            $command,
            'unexpected indexer command',
        );
    }
}
