<?php

namespace PabaTests\Suit\Functional;

use Paba\Configurator\PabaConfigurator;
use Paba\PabaFacade;
use Paba\PabaFactory;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class PabaFacadeTest extends TestCase
{
    protected string $configFile = \PABA_TESTS_FIXTURE_DIR . 'scenario.ini';

    protected string $outputCsv = '/tmp/paba_output.csv';

    protected function setUp(): void
    {
        @unlink($this->outputCsv);
    }

    public function testAnalyse()
    {
        $output = new ConsoleOutput();
        $output->setVerbosity(OutputInterface::VERBOSITY_VERY_VERBOSE);

        $factory = new PabaFactory();
        $factory->setOutput($output);

        $facade = new PabaFacade();
        $facade->setFactory($factory);

        $configurator = (new PabaConfigurator())
            ->setConfigFile($this->configFile)
            ->setOutputFile($this->outputCsv);

        $facade->generate($configurator);

        $this->assertFileExists($this->outputCsv);
    }
}
