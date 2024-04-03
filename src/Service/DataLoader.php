<?php

namespace App\Service;

use Generator;

interface DataLoader
{
    public function readData(): Generator;
}