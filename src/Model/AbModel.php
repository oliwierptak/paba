<?php

declare(strict_types = 1);

namespace Paba\Model;

use Exception;
use Generator;
use Paba\Configurator\PabaScenario;
use Paba\Model\Helper\Reporter;
use function escapeshellarg;
use function shell_exec;

class AbModel
{
    protected Reporter $reporter;

    public function __construct(Reporter $reporter)
    {
        $this->reporter = $reporter;
    }

    /**
     * @param \Paba\Configurator\PabaScenario $scenario
     *
     * @return \Generator
     * @throws \Exception
     */
    public function run(PabaScenario $scenario): Generator
    {
        $concurrency = $scenario->getConcurrency();
        for ($a = 0; $a < $scenario->getRepeat(); $a++) {
            $headerOption = '';
            foreach ($scenario->getHeaders() as $name => $value) {
                $headerOption .= sprintf('-H "%s:%s" ', $name, $value);
            }

            $ab = sprintf('ab -d -q %s -s %d -n %d -c %d %s',
                $headerOption,
                $scenario->getTimeout(),
                $scenario->getRun(),
                $concurrency,
                escapeshellarg($scenario->getHost() . $scenario->getUrl())
            );

            $this->reporter->reportAb($scenario, $a + 1, $concurrency);

            $output = shell_exec($ab);

            if ($output === null) {
                throw new Exception('Error while executing ab: ' . $ab);
            }

            $concurrency += $scenario->getStep();

            yield $output;
        }
    }
}
