<?php

declare(strict_types = 1);

namespace Paba;

use Paba\Configurator\ScenarioContainer;
use Paba\Model\AbModel;
use Paba\Model\AnalyseModel;
use Paba\Model\Helper\Reporter;
use Paba\Model\PabaModel;
use Paba\Model\WriterModel;
use Paba\Plugin\CompleteRequestsPlugin;
use Paba\Plugin\ConcurrencyLevelPlugin;
use Paba\Plugin\DocumentLengthPlugin;
use Paba\Plugin\DocumentPathPlugin;
use Paba\Plugin\FailedRequestsPlugin;
use Paba\Plugin\HtmlTransferredPlugin;
use Paba\Plugin\RequestsPerSecondPlugin;
use Paba\Plugin\TimePerRequestPlugin;
use Paba\Plugin\TImeTakenForTestsPlugin;
use Paba\Plugin\TotalTransferredPlugin;
use Paba\Plugin\TransferRatePlugin;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

class PabaFactory
{
    protected ?OutputInterface $output = null;

    protected function getOutput(): OutputInterface
    {
        if (empty($this->output)) {
            $this->output = new ConsoleOutput();
        }

        return $this->output;
    }

    public function setOutput(OutputInterface $output): void
    {
        $this->output = $output;
    }

    public function createPabaModel(): PabaModel
    {
        return new PabaModel(
            $this->createAbModel(),
            $this->createAnalyseModel(),
            $this->createWriterModel(),
            $this->createScenarioContainer(),
            $this->createReporter()
        );
    }

    protected function createAbModel(): AbModel
    {
        return new AbModel(
            $this->createReporter()
        );
    }

    protected function createAnalyseModel(): AnalyseModel
    {
        return new AnalyseModel(
            $this->createPluginCollection()
        );
    }

    protected function createPluginCollection(): array
    {
        $pluginCollection = [];
        $pluginCollection[DocumentPathPlugin::class] = new DocumentPathPlugin();
        $pluginCollection[DocumentLengthPlugin::class] = new DocumentLengthPlugin();
        $pluginCollection[ConcurrencyLevelPlugin::class] = new ConcurrencyLevelPlugin();
        $pluginCollection[TImeTakenForTestsPlugin::class] = new TImeTakenForTestsPlugin();
        $pluginCollection[CompleteRequestsPlugin::class] = new CompleteRequestsPlugin();
        $pluginCollection[FailedRequestsPlugin::class] = new FailedRequestsPlugin();
        $pluginCollection[TotalTransferredPlugin::class] = new TotalTransferredPlugin();
        $pluginCollection[HtmlTransferredPlugin::class] = new HtmlTransferredPlugin();
        $pluginCollection[RequestsPerSecondPlugin::class] = new RequestsPerSecondPlugin();
        $pluginCollection[TimePerRequestPlugin::class] = new TimePerRequestPlugin();
        $pluginCollection[TransferRatePlugin::class] = new TransferRatePlugin();

        return $pluginCollection;
    }

    protected function createWriterModel(): WriterModel
    {
        return new WriterModel();
    }

    protected function createScenarioContainer(): ScenarioContainer
    {
        return new ScenarioContainer();
    }

    private function createReporter(): Reporter
    {
        return new Reporter(
            $this->getOutput()
        );
    }
}
