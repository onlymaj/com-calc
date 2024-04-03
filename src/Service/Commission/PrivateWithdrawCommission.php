<?php
namespace App\Service\Commission;
use App\Entity\Transaction;
use App\Service\Commission\CommisssionTypeInterface;

class PrivateWithdrawCommission implements CommisssionTypeInterface
{
    private $fee = 0.003;
    private $baseLimit = 1000;

    public function calculate(Transaction $transaction, float $totalWeekTransactions): float
    {
        if($totalWeekTransactions > $this->baseLimit) {
            return $this->fee * $transaction->getBaseAmount();
        }

        $amount = $transaction->getBaseAmount() + $totalWeekTransactions - $this->baseLimit;
        if($amount > 0) {
            $fee = $this->fee * $amount;
            return $fee;
        }      
            
        else {
            return 0.00;
        }  
    }
}