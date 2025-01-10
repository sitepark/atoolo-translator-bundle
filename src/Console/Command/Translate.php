<?php

declare(strict_types=1);

namespace Atoolo\Translator\Console\Command;

use Atoolo\Translator\Console\Command\Io\TypifiedInput;
use Atoolo\Translator\Dto\Format;
use Atoolo\Translator\Dto\TranslationParameter;
use Atoolo\Translator\Service\Translator;
use Psr\Cache\InvalidArgumentException;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'translator:translate',
    description: 'translate text, and cache the result',
)]
class Translate extends Command
{
    public function __construct(
        private readonly Translator $translator,
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this
            ->setHelp('Command to translate a text.')
            ->addArgument(
                'text',
                InputArgument::REQUIRED,
                'Text to translate.',
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
            )
            ->addOption(
                'format',
                null,
                InputOption::VALUE_OPTIONAL,
                'text | html',
                'text',
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
        $format = Format::from($typedInput->getStringOption('format'));

        $parameter = new TranslationParameter(
            sourceLang: $sourceLang,
            targetLang: $targetLang,
            format: $format,
        );

        $translated = $this->translator->translate([$text], $parameter);

        $io->text($translated[0]);

        return Command::SUCCESS;
    }
}
