<?php

namespace App\Command;

use App\Service\CSVParser;
use App\Entity\Transaction;
use App\Service\CurrencyDetector;
use App\Service\CommissionCalculator;
use App\Service\Exchange\ExchangeApi;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

#[AsCommand(name: 'app:calculate-commission', description: 'Calculate commission based on input file')]
class CalculateCommisionCommand extends Command
{
    private string $csvinput = 'input.csv';

    protected function configure(): void
    {
        $this->addArgument('inputcsv', InputArgument::OPTIONAL, 'CSV file to parse');
    }

    public function __construct(#[Autowire('%app.exchange%')] private $config,
        private CSVParser $csvParser, 
        private ExchangeApi $exchangeApi, 
        private CommissionCalculator $commissionCalculator
    ) {
        parent::__construct();
    }
    

    protected function execute(InputInterface $input, OutputInterface $output): int
    {

        $inputFile = $input->getArgument('inputcsv') ?? $this->csvinput;
        $output->writeln(
            [
            'Input file: ' . $inputFile,
            '==========================='
            ]
        );

        try {
            $rates = $this->exchangeApi->getRates();
            $parser = $this->csvParser->setFileName($inputFile);
            foreach ($parser->readData() as $row) {
                $transaction = new Transaction();

                $transaction->setDate($row[0])
                    ->setUserId($row[1])
                    ->setCustomerType($row[2])
                    ->setTransactionType($row[3])
                    ->setAmount($row[4])
                    ->setCurrency($row[5]);

                $fee = $this->commissionCalculator->calculateFee($transaction, $rates[$transaction->getCurrency()]);
                if(CurrencyDetector::hasSubunit($transaction->getCurrency())) {
                    $output->writeln($fee);
                } else { 
                    $output->writeln(number_format($fee, $this->config['precision'], '.', ''));
                }
            }
        } catch (\Exception $e) {
            $output->writeln($e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
