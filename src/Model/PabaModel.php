<?php

declare(strict_types = 1);

namespace Paba\Model;

use LogicException;
use Paba\Configurator\PabaConfigurator;
use Paba\Configurator\PabaItem;
use Paba\Configurator\PabaScenario;
use Paba\Configurator\ScenarioContainer;
use Paba\Model\Helper\ProgressIndicator;
use Symfony\Component\Console\Output\OutputInterface;

class PabaModel
{
    protected AbModel $ab;

    protected AnalyseModel $analyser;

    protected WriterModel $writer;

    protected OutputInterface $output;

    protected ScenarioContainer $scenarioContainer;

    public function __construct(
        AbModel $ab,
        AnalyseModel $analyser,
        WriterModel $writer,
        ScenarioContainer $scenarioContainer,
        OutputInterface $output
    )
    {
        $this->ab = $ab;
        $this->analyser = $analyser;
        $this->writer = $writer;
        $this->scenarioContainer = $scenarioContainer;
        $this->output = $output;
    }

    public function generate(PabaConfigurator $configurator): void
    {
        $this->output->writeln(sprintf(
            'Generating output under: <fg=yellow>%s</>',
            $configurator->getOutputFile()
        ));

        $this->output->writeln('');

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

        $progressIndicator = new ProgressIndicator($this->output, $configurator);
        $progressIndicator->start($max);

        foreach ($configurator->getScenarios() as $scenario) {
            $this->validateScenario($scenario);

            if ($scenario->getRepeat() < 1) {
                continue;
            }

            $data = $this->generateScenarioStats($scenario);
            foreach ($this->ab->run($scenario, $progressIndicator) as $abResult) {
                $data = array_merge($data, $this->analyser->analyse($abResult));

                $this->writer->writeHeader($configurator->getOutputFile(), $data);
                $this->writer->write($configurator->getOutputFile(), $data);
            }

            if ($scenario->hasSleep()) {
                $progressIndicator->message('%scenario_sleep:-8s%');
                sleep($scenario->getSleep());
            }
        }

        $progressIndicator->stop(
            "<fg=green>✔</> All done in %elapsed%\n"
        );
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
