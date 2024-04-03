<?php

namespace App\Service\Exchange;

use App\Service\Exchange\ExchangeInterface;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;


class ExchangeApi implements ExchangeInterface
{

    private array $rates = [];

    public function __construct(
        #[Autowire('%app.exchange%')]
        private array $config,
        private HttpClientInterface $httpClient,
    ) {
        $exchangeFile = $config['exchangefile'];
        if(file_exists($exchangeFile)) {
            $data = $this->loadData();
            $lastUpdate = new \DateTime();
            $lastUpdate = $lastUpdate->setTimestamp($data['timestamp']);
            if($this->needsUpdate($lastUpdate) && $config['auto_update']) {
                $data = $this->updateExchangeFile();
            }
        }
        else if($config['auto_update']) {
            $data = $this->updateExchangeFile();
        }
        $this->rates = $data['rates'];
    }

    public function getRates(): array
    {
        return $this->rates;
    }

    private function loadData()
    {
        return json_decode(file_get_contents($this->config['exchangefile']), true);
    }
    
    private function updateExchangeFile(): array
    {
        $data = $this->fetchData();
        file_put_contents($this->config['exchangefile'], $data);
        return json_decode($data, true);
    }

    private function needsUpdate($lastUpdate): bool
    {
        $currentDate = new \DateTime();
        $timeDiff = $lastUpdate->diff($currentDate);
        // check if the last update time is more than 30 minutes
        if($timeDiff->days > 0 || $timeDiff->h > 0 || $timeDiff->m > 30) {
            return true;
        }
        return false;
    }

    private function fetchData(): string
    {
        $client = $this->httpClient->withOptions(
            [
            'headers'   => [
                'Content-Type' => 'text/plain',
                'apikey' => $this->config['apikey'],
            ],
            ]
        );

        $response = $client->request('GET', 'https://api.apilayer.com/exchangerates_data/latest?base='. $this->config['base_currency']);

        if ($response->getStatusCode() !== 200) {
            throw new \ErrorException(sprintf('ExchangeRateApiProvider failed to retrieve exchange rates, code %s', $response->getStatusCode()));
        }

        return $response->getContent();
    }

}
