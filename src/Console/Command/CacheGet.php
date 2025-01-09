<?php

declare(strict_types=1);

namespace Atoolo\Translator\Console\Command;

use Atoolo\Translator\Console\Command\Io\TypifiedInput;
use Atoolo\Translator\Dto\Format;
use Atoolo\Translator\Dto\TranslationParameter;
use Atoolo\Translator\Service\TextHasher;
use Psr\Cache\CacheItemPoolInterface;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\Cache\CacheInterface;

#[AsCommand(
    name: 'translator:cache:get',
    description: 'get cached translations',
)]
class CacheGet extends Command
{
    public function __construct(
        private readonly CacheItemPoolInterface $translationCache,
        #[Autowire(service: 'atoolo_translator.textHasher')]
        private readonly TextHasher $textHasher,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Command to list cached translations.')
            ->addArgument(
                'text',
                InputArgument::REQUIRED,
                'Text whose translation is to be retrieved from the cache.',
            )
            ->addOption(
                'sourceLang',
                null,
                InputOption::VALUE_OPTIONAL,
                'Language to be used. (de, en, fr, it, ...)',
                'de',
            )
            ->addOption(
                'targetLang',
                'l',
                InputOption::VALUE_REQUIRED,
                'Language to be used. (de, en, fr, it, ...)',
            );
    }

    /**
     * @throws InvalidArgumentException
     */
    protected function execute(
        InputInterface $input,
        OutputInterface $output,
    ): int {

        $typedInput = new TypifiedInput($input);
        $io = new SymfonyStyle($input, $output);

        $sourceLang = $typedInput->getStringOption('sourceLang');
        $targetLang = $typedInput->getStringOption('targetLang');
        $text = $typedInput->getStringArgument('text');

        $translationParamter = new TranslationParameter($sourceLang, $targetLang, Format::TEXT);
        $hash = $this->textHasher->hash($text, $translationParamter);

        if (!$this->translationCache->hasItem($hash)) {
            $io->text('No translation found in cache.');
            return Command::SUCCESS;
        }

        $item = $this->translationCache->getItem($hash);

        if (!$item->isHit()) {
            $io->text('No translation found in cache.');
            return Command::SUCCESS;
        }

        /** @var string $translated */
        $translated = $item->get();

        $io->text($translated);

        return Command::SUCCESS;
    }
}
