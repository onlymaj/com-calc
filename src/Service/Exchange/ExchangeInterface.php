<?php 

namespace App\Service\Exchange;

interface ExchangeInterface
{

    public function getRates(): array;
    
}