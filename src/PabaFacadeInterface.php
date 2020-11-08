<?php

namespace Paba;

use Paba\Configurator\PabaConfigurator;

interface PabaFacadeInterface
{
    const VERSION = '1.0';

    public function setFactory(PabaFactory $factory): void;

    /**
     * Specification
     * - Run ab command according to scenario settings
     * - Collect and parse ab output
     * - Generate output file in CSV format
     *
     * @param \Paba\Configurator\PabaConfigurator $configurator
     *
     * @return void
     */
    public function generate(PabaConfigurator $configurator): void;
}
