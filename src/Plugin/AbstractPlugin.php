<?php

declare(strict_types = 1);

namespace Paba\Plugin;

use Paba\Configurator\PabaItem;
use function strcasecmp;

abstract class AbstractPlugin
{
    protected const TITLE = null;

    abstract public function run(PabaItem $item): PabaItem;

    public function canRun(PabaItem $item): bool
    {
        return strcasecmp($item->getTitle(), static::TITLE) === 0;
    }
}
