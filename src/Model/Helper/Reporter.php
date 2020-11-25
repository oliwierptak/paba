<?php

declare(strict_types = 1);

namespace Paba\Model\Helper;

use Paba\Configurator\PabaConfigurator;
use Paba\Configurator\PabaScenario;
use Symfony\Component\Console\Output\OutputInterface;

class Reporter
{
    protected OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    public function announce(PabaConfigurator $configurator)
    {
        if (!$this->shouldOutput()) {
            return;
        }

        $this->output->writeln(sprintf(
            'Generating output under: <fg=yellow>%s</>',
            $configurator->getOutputFile()
        ));

        $this->output->writeln('');
    }

    public function reportAb(PabaScenario $scenario, int $iteration, int $concurrency): void
    {
        if (!$this->shouldOutputAb()) {
            return;
        }

        $this->output->writeln(sprintf(
            'Executing ab[%d]: <fg=cyan>%s</>, n:<fg=cyan>%d</>, c:<fg=cyan>%d</>',
            $iteration,
            $scenario->getHost(),
            $scenario->getRun(),
            $concurrency
        ));
    }

    public function reportScenario(PabaScenario $scenario): void
    {
        if (!$this->shouldOutput()) {
            return;
        }

        $this->output->writeln(sprintf(
            'Running scenario: <fg=yellow>%s</>',
            $scenario->getName(),
        ));
    }

    public function sleep(PabaScenario $scenario): void
    {
        if (!$this->shouldOutput()) {
            return;
        }

        $this->output->writeln(sprintf(
            'Sleeping for <fg=yellow>%d</>',
            $scenario->getSleep()
        ));
    }

    public function done(): void
    {
        if (!$this->shouldOutput()) {
            return;
        }

        $this->output->writeln(sprintf(
            'All done',
        ));
    }

    protected function shouldOutput(): bool
    {
        return $this->output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL;
    }

    protected function shouldOutputAb(): bool
    {
        return $this->output->getVerbosity() >= OutputInterface::VERBOSITY_VERBOSE;
    }
}

