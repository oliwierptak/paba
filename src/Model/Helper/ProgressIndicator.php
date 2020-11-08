<?php declare(strict_types = 1);

namespace Paba\Model\Helper;

use Paba\Configurator\PabaConfigurator;
use Paba\Configurator\PabaScenario;
use Symfony\Component\Console\Helper\ProgressBar;
use Symfony\Component\Console\Output\OutputInterface;

class ProgressIndicator
{
    protected OutputInterface $output;

    protected PabaConfigurator $configurator;

    protected ?ProgressBar $progressBar;

    public function __construct(OutputInterface $output, PabaConfigurator $configurator)
    {
        $this->output = $output;
        $this->configurator = $configurator;
    }

    public function start(int $max = 0): void
    {
        if (!$this->showProgressBar()) {
            return;
        }

        ProgressBar::setPlaceholderFormatterDefinition(
            'output_file',
            function (ProgressBar $progressBar, OutputInterface $output) {
                return $this->configurator->getOutputFile();
            }
        );

        $this->progressBar = new ProgressBar($this->output, $max);
        $this->progressBar->setFormat(
            "%current:3s%/%max:-3s% %bar% %percent:3s%% (%remaining%)\n"
        );

        //$this->output->writeln(sprintf('Generating: <fg=yellow>%s</>', $this->configurator->getOutputFile()));

        $this->progressBar->setBarWidth(55);
        $this->progressBar->start();
    }

    protected function showProgressBar(): bool
    {
        return $this->output->getVerbosity() >= OutputInterface::VERBOSITY_NORMAL;
    }

    public function advance(PabaScenario $scenario, int $step): void
    {
        if (!$this->showProgressBar()) {
            return;
        }

        ProgressBar::setPlaceholderFormatterDefinition(
            'scenario_name',
            function (ProgressBar $progressBar, OutputInterface $output) use ($scenario) {
                return $scenario->getName();
            }
        );
        ProgressBar::setPlaceholderFormatterDefinition(
            'scenario_comment',
            function (ProgressBar $progressBar, OutputInterface $output) use ($scenario) {
                return trim($scenario->getComment()) === '' ? '' : ' - ' . $scenario->getComment();
            }
        );
        ProgressBar::setPlaceholderFormatterDefinition(
            'scenario_sleep',
            function (ProgressBar $progressBar, OutputInterface $output) use ($scenario) {
                return sprintf('Sleeping after scenario: <fg=yellow>%s</> for <fg=yellow>%ds</>',
                    $scenario->getName(),
                    $scenario->getSleep()
                );
            }
        );
        ProgressBar::setPlaceholderFormatterDefinition(
            'scenario_repeat',
            function (ProgressBar $progressBar, OutputInterface $output) use ($scenario) {
                return $scenario->getRepeat();
            }
        );

        ProgressBar::setPlaceholderFormatterDefinition(
            'scenario_step',
            function (ProgressBar $progressBar, OutputInterface $output) use ($step) {
                return $step;
            }
        );

        $this->progressBar->setFormat(
            "%current:3s%/%max:-3s% %bar% %percent:3s%% (%remaining%)\n%scenario_step:3s%/%scenario_repeat:-3s% <fg=cyan>%scenario_name%</>%scenario_comment%"
        );

        $this->progressBar->advance();
    }

    public function stop(string $format = "%elapsed% %memory:56s%"): void
    {
        if (!$this->showProgressBar()) {
            return;
        }

        $this->progressBar->setFormat($format);
        $this->progressBar->finish();
    }

    public function message(string $message): void
    {
        if (!$this->showProgressBar()) {
            return;
        }

        $this->progressBar->setFormat($message);
        $this->progressBar->display();
    }

    public function display(): void
    {
        if (!$this->showProgressBar()) {
            return;
        }

        $this->progressBar->display();
    }
}
