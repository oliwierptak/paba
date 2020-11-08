<?php

declare(strict_types = 1);

namespace Paba;

use Paba\Configurator\PabaConfigurator;

class PabaFacade implements PabaFacadeInterface
{
    protected PabaFactory $factory;

    protected function getFactory(): PabaFactory
    {
        if (empty($this->factory)) {
            $this->factory = new PabaFactory();
        }

        return $this->factory;
    }

    public function setFactory(PabaFactory $factory): void
    {
        $this->factory = $factory;
    }

    public function generate(PabaConfigurator $configurator): void
    {
        $this->getFactory()
            ->createPabaModel()
            ->generate($configurator);
    }
}
