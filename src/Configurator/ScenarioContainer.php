<?php declare(strict_types = 1);

namespace Paba\Configurator;

use LogicException;

class ScenarioContainer
{
    protected array $data = [];

    /**
     * @param string $configFilename
     * @param string $name
     *
     * @return \Paba\Configurator\PabaScenario
     */
    public function getScenarioByName(string $configFilename, string $name): PabaScenario
    {
        $config = $this->getScenarios($configFilename)[$name] ?? null;

        if (!($config instanceof PabaScenario)) {
            throw new LogicException(
                sprintf(
                    'Unknown config section: "%s". Available sections: %s',
                    $name,
                    implode(', ', array_keys($this->getData($configFilename)))
                )
            );
        }

        return $config;
    }

    /**
     * @param string $configFilename
     *
     * @return \Paba\Configurator\PabaScenario[]
     */
    public function getScenarios(string $configFilename): array
    {
        $result = [];
        foreach ($this->getData($configFilename) as $name => $data) {
            $scenario = (new PabaScenario())
                ->fromArray($data)
                ->setName($name);
            
            $result[$name] = $scenario;
        }

        return $result;
    }

    protected function getData(string $configFilename): array
    {
        if (empty($this->data[$configFilename])) {
            $this->data[$configFilename] = $this->loadConfig($configFilename);
        }

        return $this->data[$configFilename];
    }

    protected function loadConfig(string $configFilename): array
    {
        if (!is_file($configFilename)) {
            throw new LogicException(
                sprintf(
                    'Config file: "%s" not found',
                    $configFilename
                )
            );
        }

        $data = parse_ini_file($configFilename, true) ?? [];
        if ($data === false) {
            $data = [];
        }

        return $data;
    }
}
