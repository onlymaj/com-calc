<?php

namespace App\Service;

use Generator;
use App\Service\DataLoader;

class CSVParser implements DataLoader
{
    private $fileName = null;
    public function setFileName($fileName): self
    {
        $this->fileName = $fileName;
        return $this;
    }

    public function readData(): Generator
    {
        $handle = fopen($this->fileName, "r");
        if ($handle !== false) {
            while (($row = fgetcsv($handle)) !== false) {
                yield $row;
            }
            fclose($handle);
        }
    }
}
