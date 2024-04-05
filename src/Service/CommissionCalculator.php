<?php

namespace App\Service;

use App\Entity\Transaction;
use App\Service\CurrencyDetector;
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
        $userId = $transaction->getUserId();
        $week = $transaction->getTransactionWeek();
        $type = $transaction->getTransactionType();
        $amount = $transaction->getBaseAmount();

        $userTransactions = &$this->userSumTransactions[$userId];

        if (isset($userTransactions[$week][$type])) {
            $userTransactions[$week][$type] += $amount;
        } else {
            $userTransactions[$week][$type] = $amount;
        }
    }

    public function calculateFee(Transaction $transaction, float $exchangeRate): float
    {
        $commissionType = $this->getCommissionType($transaction);
        $userId = $transaction->getUserId();
        $week = $transaction->getTransactionWeek();
        $type = $transaction->getTransactionType();
        $weekTotal = $this->userSumTransactions[$userId][$week][$type] ?? 0;

        $amount = $this->toBaseCurrency($transaction->getAmount(), $exchangeRate);
        $transaction->setBaseAmount($amount);

        $fee = $commissionType->calculate($transaction, $weekTotal);
        $fee = $this->roundUp($fee * $exchangeRate, $this->config['precision']);

        if (CurrencyDetector::hasSubunit($transaction->getCurrency())) {
            $fee = (int) ceil($fee);
        }

        $this->addWeeklyTotal($transaction);

        return $fee;
    }
}
