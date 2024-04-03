<?php

namespace App\Service\Commission;

use App\Entity\Transaction;

interface CommisssionTypeInterface
{
    public function calculate(Transaction $transaction, float $totalWeekTransactions): float;
}
