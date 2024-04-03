<?php
namespace App\Service\Commission;
use App\Entity\Transaction;
use App\Service\Commission\CommisssionTypeInterface;

class BusinessWithdrawCommission implements CommisssionTypeInterface
{
    private $commissionPercent = 0.005;
    public function calculate(Transaction $transaction, float $totalWeekTransactions): float
    {
        
        return $this->commissionPercent * $transaction->getAmount();
    }
}