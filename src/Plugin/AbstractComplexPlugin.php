<?php

declare(strict_types = 1);

namespace Paba\Plugin;

use Paba\Configurator\PabaItem;
use function preg_match;

abstract class AbstractComplexPlugin extends AbstractPlugin
{
    protected const REGEX = '@([0-9\.]+) ([a-zA-Z]+)@';

    public function run(PabaItem $item): PabaItem
    {
        $matches = [];
        preg_match(static::REGEX, $item->getValue(), $matches, \PREG_OFFSET_CAPTURE);

        $item->setValue($matches[1][0]);
        $item->setUnit($matches[2][0]);

        return $item;
    }
}
