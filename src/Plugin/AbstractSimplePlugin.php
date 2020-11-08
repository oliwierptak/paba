<?php

declare(strict_types = 1);

namespace Paba\Plugin;

use Paba\Configurator\PabaItem;

abstract class AbstractSimplePlugin extends AbstractPlugin
{
    public function run(PabaItem $item): PabaItem
    {
        return $item;
    }
}
