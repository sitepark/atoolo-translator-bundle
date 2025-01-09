<?php

declare(strict_types=1);

namespace Atoolo\Translator\Dto;

enum Format: string
{
    case TEXT = 'text';
    case HTML = 'html';
}
