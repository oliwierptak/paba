<?php declare(strict_types = 1);

namespace Paba\Command;

use InvalidArgumentException;
use Paba\Configurator\PabaConfigurator;
use Paba\Configurator\ScenarioContainer;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class GenerateCommand extends AbstractCommand
{
    const COMMAND_NAME = 'generate';
    const COMMAND_DESCRIPTION = 'Generate CSV report';
    const ARGUMENT_CONFIG_SECTION_NAME = 'configSectionName';
    const OPTION_CONFIG_FILENAME = 'configFile';
    const OPTION_OUTPUT_FILENAME = 'outputFile';

    protected function configure(): void
    {
        $this
            ->setName(static::COMMAND_NAME)
            ->setDescription(static::COMMAND_DESCRIPTION)
            ->addOption(static::OPTION_CONFIG_FILENAME, 'c', InputOption::VALUE_REQUIRED, 'Config filename')
            ->addOption(static::OPTION_OUTPUT_FILENAME, 'o', InputOption::VALUE_REQUIRED, 'Output filename')
            ->addArgument(static::ARGUMENT_CONFIG_SECTION_NAME, InputOption::VALUE_OPTIONAL, 'Config section name');
    }

    protected function executeCommand(InputInterface $input, OutputInterface $output): int
    {
        $input->validate();

        $configurator = $this->buildConfigurator($input, $output);
        $this->facade->generate($configurator);

        return 0;
    }

    protected function buildConfigurator(
        InputInterface $input,
        OutputInterface $output
    ): PabaConfigurator
    {
        $value = trim((string) $input->getOption(static::OPTION_CONFIG_FILENAME));
        if ($value === '') {
            throw new InvalidArgumentException('Required value missing for: ' . static::OPTION_CONFIG_FILENAME);
        }

        $value = trim((string) $input->getOption(static::OPTION_OUTPUT_FILENAME));
        if ($value === '') {
            throw new InvalidArgumentException('Required value missing for: ' . static::OPTION_OUTPUT_FILENAME);
        }

        $configurator = (new PabaConfigurator())
            ->setConfigFile($input->getOption(static::OPTION_CONFIG_FILENAME))
            ->setOutputFile($input->getOption(static::OPTION_OUTPUT_FILENAME));

        $scenarioContainer = new ScenarioContainer();
        $arguments = [
            /*static::OPTION_CONFIG_FILENAME => $input->getOption(static::OPTION_CONFIG_FILENAME),
            static::OPTION_OUTPUT_FILENAME => $input->getOption(static::OPTION_OUTPUT_FILENAME),
            static::OPTION_URL => $input->getOption(static::OPTION_URL),
            static::OPTION_HOST => $input->getOption(static::OPTION_HOST),
            static::OPTION_RUN => $input->getOption(static::OPTION_RUN),
            static::OPTION_CONCURRENCY => $input->getOption(static::OPTION_CONCURRENCY),
            static::OPTION_STEP => $input->getOption(static::OPTION_STEP),
            static::OPTION_REPEAT => $input->getOption(static::OPTION_REPEAT),
            static::OPTION_TIMEOUT => $input->getOption(static::OPTION_TIMEOUT),
            static::OPTION_SLEEP => $input->getOption(static::OPTION_SLEEP),*/
        ];

        $items = $scenarioContainer->getScenarios($configurator->getConfigFile());
        $configSections = $input->getArgument(static::ARGUMENT_CONFIG_SECTION_NAME);

        if (!empty($configSections)) {
            $selectedItems = [];
            foreach ($configSections as $name) {
                $selectedItems[$name] = $items[$name];
            }

            $items = $selectedItems;
        }

        $configurator->setScenarios($items);

        return $configurator;
    }
}
