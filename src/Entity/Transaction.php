<?php

namespace App\Entity;

class Transaction
{
    protected \DateTimeInterface $date;

    protected int $userId;

    protected string $customerType;

    protected string $transactionType;

    protected float $amount;

    protected float $baseAmount;
    
    protected string $currency;

    protected float $exchangeRate = 1;

    public function getDate(): \DateTime
    {
        return $this->date;
    }
   
    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getBaseAmount(): float
    {
        return $this->baseAmount;
    }

    public function getExchangeRate(): float
    {
        return $this->exchangeRate;
    }

    public function getCustomerType(): string
    {
        return $this->customerType;
    }

    public function getTransactionType(): string
    {
        return $this->transactionType;
    }

    public function getAmount(): float
    {
        return $this->amount;
    }

    public function getTransactionWeek(): int
    {
        return (int)$this->getDate()->format('Wo');
    }


    public function getCurrency(): string
    {
        return $this->currency;
    }

    public function setUserId(int $userId): self 
    {
        $this->userId = $userId;

        return $this;
    }

    public function setDate(string $date): self 
    {
        $this->date = \DateTime::createFromFormat('Y-m-d', $date);

        return $this;
    }

    public function setTransactionType(string $transactionType): self
    {
        $this->transactionType = $transactionType;

        return $this;
    }

    public function setCurrency(string $currency): self
    {
        $this->currency = $currency;

        return $this;
    }

    public function setAmount(float $amount): self
    {
        $this->amount = $amount;

        return $this;
    }

    public function setBaseAmount(float $amount): self
    {
        $this->baseAmount = $amount;
        return $this;
    }


    public function setExchangeRate(float $exchangeRate): self
    {
        $this->exchangeRate = $exchangeRate;
        return $this;
    }

    public function setCustomerType(string $customerType): self
    {
        $this->customerType = $customerType;

        return $this;
    }

}