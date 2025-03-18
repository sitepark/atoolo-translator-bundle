<?php

declare(strict_types=1);

namespace Atoolo\Translator\Console\Command\Io;

use InvalidArgumentException;
use Symfony\Component\Console\Input\InputInterface;

class TypifiedInput
{
    public function __construct(private readonly InputInterface $input) {}

    public function getStringOption(string $name): string
    {
        $value = $this->input->getOption($name);
        if (!is_string($value)) {
            throw new InvalidArgumentException(
                'option ' . $name . ' must be a string: ' . $value,
            );
        }
        return $value;
    }

    public function getStringArgument(string $name): string
    {
        $value = $this->input->getArgument($name);
        if (!is_string($value)) {
            throw new InvalidArgumentException(
                'argument ' . $name . ' must be a string',
            );
        }
        return $value;
    }
}
