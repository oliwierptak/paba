<?php

declare(strict_types = 1);

namespace Paba\Model;

use InvalidArgumentException;
use Paba\Configurator\PabaItem;
use const PHP_EOL;

class AnalyseModel
{
    /**
     * @var \Paba\Plugin\AbstractPlugin[]
     */
    protected array $pluginCollection;

    public function __construct(array $pluginCollection)
    {
        $this->pluginCollection = $pluginCollection;
    }

    public function analyse(string $input): array
    {
        $input = trim($input);
        $lines = explode(PHP_EOL, $input);

        if ($lines === false || !is_array($lines) || count($lines) < 1) {
            throw new InvalidArgumentException(sprintf(
                'Can\'t read input: "%s"',
                $input
            ));
        }

        $result = [];
        foreach ($lines as $line) {
            if (trim($line) === '') {
                continue;
            }

            foreach ($this->pluginCollection as $pluginName => $plugin) {
                $title = $this->extractTitle($line);
                $value = $this->extractValue($line);

                $item = (new PabaItem())
                    ->setTitle($title)
                    ->setValue($value);

                if (!$plugin->canRun($item)) {
                    continue;
                }

                $item = $plugin->run($item);

                $result[$pluginName] = $item;
            }
        }

        return $result;
    }

    protected function extractTitle(string $line): string
    {
        $tokens = explode(':', $line);

        return trim((string) array_shift($tokens));
    }

    protected function extractValue(string $line): string
    {
        $tokens = explode(':', $line);

        return trim((string) array_pop($tokens));
    }
}
