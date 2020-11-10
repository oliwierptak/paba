<?php

declare(strict_types = 1);

namespace Paba\Model;

use LogicException;
use Paba\Configurator\PabaConfigurator;
use Paba\Configurator\PabaItem;
use Paba\Configurator\PabaScenario;
use Paba\Configurator\ScenarioContainer;
use Paba\Model\Helper\Reporter;

class PabaModel
{
    protected AbModel $ab;

    protected AnalyseModel $analyser;

    protected WriterModel $writer;

    protected ScenarioContainer $scenarioContainer;

    protected Reporter $reporter;

    public function __construct(
        AbModel $ab,
        AnalyseModel $analyser,
        WriterModel $writer,
        ScenarioContainer $scenarioContainer,
        Reporter $reporter
    )
    {
        $this->ab = $ab;
        $this->analyser = $analyser;
        $this->writer = $writer;
        $this->scenarioContainer = $scenarioContainer;
        $this->reporter = $reporter;
    }

    public function generate(PabaConfigurator $configurator): void
    {
        $this->reporter->announce($configurator);

        $this->validate($configurator);

        if (!$configurator->hasScenarios()) {
            $configurator->setScenarios(
                $this->scenarioContainer->getScenarios($configurator->getConfigFile())
            );
        }

        $max = 0;
        foreach ($configurator->getScenarios() as $scenario) {
            $max += $scenario->getRepeat();
        }

        foreach ($configurator->getScenarios() as $scenario) {
            $this->reporter->reportScenario($scenario);
            $this->validateScenario($scenario);

            if ($scenario->getRepeat() < 1) {
                continue;
            }

            $data = $this->generateScenarioStats($scenario);
            foreach ($this->ab->run($scenario) as $abResult) {
                $data = array_merge($data, $this->analyser->analyse($abResult));

                $this->writer->writeHeader($configurator->getOutputFile(), $data);
                $this->writer->write($configurator->getOutputFile(), $data);
            }

            if ($scenario->hasSleep()) {
                $this->reporter->sleep($scenario);
                sleep($scenario->getSleep());
            }
        }

        $this->reporter->done();
    }

    protected function validate(PabaConfigurator $configurator)
    {
        $configurator->requireConfigFile();
        $configurator->requireOutputFile();
    }

    protected function validateScenario(PabaScenario $scenario)
    {
        $scenario->requireRun();
        $scenario->requireConcurrency();
        $scenario->requireStep();
        $scenario->requireHost();
        $scenario->requireRepeat();
        $scenario->requireUrl();

        if ($scenario->getRun() < 1) {
            throw new LogicException(sprintf('Invalid scenario value for run: %d', $scenario->getRun()));
        }

        if ($scenario->getConcurrency() < 1) {
            throw new LogicException(sprintf('Invalid scenario value for concurrency: %d', $scenario->getRun()));
        }
    }

    protected function generateScenarioStats($scenario): array
    {
        $data['scenario'] = (new PabaItem())
            ->setTitle('Scenario Name')
            ->setValue($scenario->getName());

        $data['runs'] = (new PabaItem())
            ->setTitle('Runs')
            ->setValue((string) $scenario->getRun());

        $data['repeat'] = (new PabaItem())
            ->setTitle('Repeat')
            ->setValue((string) $scenario->getRepeat());

        return $data;
    }
}
