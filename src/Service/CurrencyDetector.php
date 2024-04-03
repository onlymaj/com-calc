<?php

namespace App\Service;

class CurrencyDetector
{
    private const CURR_WITH_NO_SUBUNIT = ['JPY', 'KRW', 'ISK','CLP','PYG','UYU','UZS','VND','VUV','XAF','XOF','YER','MGA','MMK','KHR','KMF','CVE','DJF','GNF','IQD','LAK','MRO'];

    /**
     * Detects currencies with three-letter codes that do not have subunits.
     *
     * @param array $currencies
     * @return array
     */
    public static function hasSubunit(string $currency): bool
    {
        return in_array($currency, self::CURR_WITH_NO_SUBUNIT);
    }
}