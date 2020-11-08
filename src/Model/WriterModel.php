<?php

declare(strict_types = 1);

namespace Paba\Model;

use function fputcsv;

class WriterModel
{
    protected bool $headerWritten = false;

    public function writeHeader(string $outputFile, array $data): void
    {
        if ($this->headerWritten) {
            return;
        }

        try {
            $header = [];
            $h = fopen($outputFile, 'w');

            foreach ($data as $name => $item) {
                $title = $item->hasUnit() ? sprintf('%s (%s)', $item->getTitle(), $item->getUnit()) : $item->getTitle();
                $header[] = $title;
            }

            fputcsv($h, $header);

            $this->headerWritten = true;
        }
        finally {
            fclose($h);
        }
    }

    public function write(string $outputFile, array $data): void
    {
        try {
            $h = fopen($outputFile, 'a+');
            $content = [];

            foreach ($data as $name => $item) {
                $content[] = $item->getValue();
            }

            fputcsv($h, $content);
        }
        finally {
            fclose($h);
        }
    }
}
