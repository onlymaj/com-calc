<?php

namespace Test;

use App\Service\Exchange\ExchangeInterface;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class CommissionFeeCommandTest extends KernelTestCase
{

    public function testExampleCSVFile(): void
    {
        self::bootKernel();
        $application = new Application(self::$kernel);
        $application->setAutoExit(false);

        $expected = [
            "Input file: input.csv",
            "===========================",
            "0.60",
            "3.00",
            "0.00",
            "0.06",
            "1.50",
            "0",
            "0.70",
            "0.30",
            "0.30",
            "3.00",
            "0.00",
            "0.00",
            "8612", 
        ];

        $command = $application->find('app:calculate-commission');
        $commandTester = new CommandTester($command);

        $commandTester->execute([
            'inputcsv' => 'input.csv'
        ]);

        $commandTester->assertCommandIsSuccessful();

        $output = $commandTester->getDisplay();
        $actual = explode(PHP_EOL, trim($output));
        $this->assertEquals($expected, $actual);
    }
}