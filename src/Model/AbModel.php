<?php

declare(strict_types = 1);

namespace Paba\Model;

use Exception;
use Generator;
use Paba\Configurator\PabaScenario;
use Paba\Model\Helper\ProgressIndicator;
use Symfony\Component\Console\Output\OutputInterface;
use function escapeshellarg;
use function shell_exec;

class AbModel
{
    protected OutputInterface $output;

    public function __construct(OutputInterface $output)
    {
        $this->output = $output;
    }

    /**
     * @param \Paba\Configurator\PabaScenario $scenario
     * @param \Paba\Model\Helper\ProgressIndicator $progressIndicator
     *
     * @return \Generator
     * @throws \Exception
     */
    public function run(PabaScenario $scenario, ProgressIndicator $progressIndicator): Generator
    {
        $concurrency = $scenario->getConcurrency();
        for ($a = 0; $a < $scenario->getRepeat(); $a++) {
            $headerOption = '';
            foreach ($scenario->getHeaders() as $name => $value) {
                $headerOption .= sprintf('-H "%s:%s" ', $name, $value);
            }

            $ab = sprintf('ab %s -s %d -n %d -c %d %s',
                $headerOption,
                $scenario->getTimeout(),
                $scenario->getRun(),
                $concurrency,
                escapeshellarg($scenario->getHost() . $scenario->getUrl())
            );

            $output = shell_exec($ab);

            $progressIndicator->advance($scenario, $a + 1);

            if ($output === null) {
                throw new Exception('Error while executing ab: ' . $ab);
            }

            $concurrency += $scenario->getStep();

            yield $output;
        }
    }
}
