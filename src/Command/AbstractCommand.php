<?php declare(strict_types = 1);

namespace Paba\Command;

use Paba\PabaFacadeInterface;
use Paba\PabaFacade;
use Paba\PabaFactory;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

abstract class AbstractCommand extends Command
{
    const COMMAND_NAME = 'unknown';
    const COMMAND_DESCRIPTION = 'unknown';
    /*
    const OPTION_URL = 'url';
    const OPTION_HOST = 'host';
    const OPTION_RUN = 'run';
    const OPTION_CONCURRENCY = 'concurrency';
    const OPTION_STEP = 'step';
    const OPTION_REPEAT = 'repeat';
    const OPTION_TIMEOUT = 'timeout';
    const OPTION_SLEEP = 'sleep';*/

    protected PabaFacadeInterface $facade;

    abstract protected function executeCommand(InputInterface $input, OutputInterface $output): int;

    protected function initialize(InputInterface $input, OutputInterface $output)
    {
        $factory = new PabaFactory();
        $factory->setOutput($output);

        $this->facade = new PabaFacade();
        $this->facade->setFactory($factory);
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $output->writeln('');
        $output->writeln(sprintf('<fg=yellow>PABA</> <fg=green>v%s</>', PabaFacadeInterface::VERSION));
        $output->writeln('');

        return $this->executeCommand($input, $output);
    }
}
