<?php
namespace Tests\App;

use App\App\CurrencyConverter;
use PHPUnit\Framework\TestCase;

class CurrencyConverterTest extends TestCase
{
    public function test_exceptions()
    {
        $EXCH = array(); 
        $EXCH['CR1']['2021-06-17'] = 4.755;
        $EXCH['CR2']['2021-06-17'] = 4.755/2;

        try {
            CurrencyConverter::convert('', 'EUR', 'CR1', 10, $EXCH);
        }
        catch(\Exception $e) {
            $this->assertMatchesRegularExpression('/^No exchange rate for currency (EUR|CR1) and date [.]$/', $e->getMessage());
        }

        try {
            CurrencyConverter::convert('2020-01-01', 'EUR', 'CR1', 10, $EXCH);
        }
        catch(\Exception $e) {
            $this->assertMatchesRegularExpression('/^No exchange rate for currency (EUR|CR1) and date 2020-01-01[.]$/', $e->getMessage());
        }

        try {
            CurrencyConverter::convert('2021-06-17', 'EUR', 'CR1', 10, $EXCH);
        }
        catch(\Exception $e) {
            $this->assertMatchesRegularExpression('/^No exchange rate for currency EUR and date 2021-06-17[.]$/', $e->getMessage());
        }

        try {
            CurrencyConverter::convert('2021-06-17', 'CR1', 'USD', 10, $EXCH);
        }
        catch(\Exception $e) {
            $this->assertMatchesRegularExpression('/^No exchange rate for currency USD and date 2021-06-17[.]$/', $e->getMessage());
        }
    }

    public function test_conversions()
    {
        $EXCH = array(); 
        $EXCH['CR1']['2021-06-17'] = 4.755;
        $EXCH['CR2']['2021-06-17'] = 4.755/2;
        $EXCH['CR3']['2021-06-18'] = 0.02;
        $EXCH['CR4']['2021-06-18'] = 1.5;

        $ret = CurrencyConverter::convert('2021-06-17', 'CR1', 'CR2', 10, $EXCH);
        $this->assertEquals(20, $ret);

        $ret = CurrencyConverter::convert('2021-06-17', 'CR1', 'CR2', 1.5, $EXCH);
        $this->assertEquals(3, $ret);

        $ret = CurrencyConverter::convert('2021-06-18', 'CR3', 'CR4', 1, $EXCH);
        $this->assertEquals((1*0.02) / 1.5, $ret);
    }
}