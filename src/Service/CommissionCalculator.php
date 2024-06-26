<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Service\CurrencySubUnitDetector;
use App\Service\Commission\CommisssionTypeInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class CommissionCalculator
{

    protected array $userSumTransactions = [];


    public function __construct(#[Autowire('%app.exchange%')] private $config)
    {
    }


    private function getCommissionType(Transaction $transaction): CommisssionTypeInterface
    {
        $type = ucfirst($transaction->getCustomerType()) . ucfirst($transaction->getTransactionType()) . 'Commission';

        return new ("App\Service\Commission\\$type");
    }

    private function toBaseCurrency($amount, $rate): float
    {
        return round($amount * (1 / $rate), $this->config['precision']);
    }

    private function roundUp($value, $precision)
    {
        $multiplier = pow(10, $precision);
        $roundedValue = round($value * $multiplier, $precision);
        return ceil($roundedValue) / $multiplier;
    }

    private function addWeeklyTotal($transaction): void
    {
        $type = $transaction->getTransactionType();
        if($type !== 'withdraw')
            return;

        $userId = $transaction->getUserId();
        $week = $transaction->getTransactionWeek();
        $amount = $transaction->getBaseAmount();

        if (isset($this->userSumTransactions[$userId][$week])) {
            $this->userSumTransactions[$userId][$week] += $amount;
        } else {
            $this->userSumTransactions[$userId][$week] = $amount;
        }
    }

    public function calculateFee(Transaction $transaction, float $exchangeRate): float
    {
        $commissionType = $this->getCommissionType($transaction);
        $userId = $transaction->getUserId();
        $week = $transaction->getTransactionWeek();
        $weekTotal = $this->userSumTransactions[$userId][$week] ?? 0;

        $amount = $this->toBaseCurrency($transaction->getAmount(), $exchangeRate);
        $transaction->setBaseAmount($amount);

        $fee = $commissionType->calculate($transaction, $weekTotal);
        $fee = $this->roundUp($fee * $exchangeRate, $this->config['precision']);

        if (CurrencySubUnitDetector::hasSubunit($transaction->getCurrency())) {
            $fee = (int) ceil($fee);
        }

        $this->addWeeklyTotal($transaction);

        return $fee;
    }
}
