<?php
namespace Tests\App;

use App\App\ExchRateLoader;
// use PHPUnit\Framework\TestCase;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class ExchRateLoaderTest extends KernelTestCase
{
    // public function test_exceptions()
    // {
        // TODO 
        // try {
        //     CurrencyConverter::convert('', 'EUR', 'CR1', 10, $EXCH);
        // }
        // catch(\Exception $e) {
        //     $this->assertMatchesRegularExpression('/^No exchange rate for currency (EUR|CR1) and date [.]$/', $e->getMessage());
        // }

    // }

    public function test_non_existing_currency()
    {
        // (1) boot the Symfony kernel
        self::bootKernel();
        // (2) use self::$container to access the service container
        $container = self::$container;

        // (3) run some service & test the result
        $ld = $container->get(ExchRateLoader::class);
        $callNBP = new \ReflectionMethod($ld, 'callNBP');
        $callNBP->setAccessible(true);

        try {
            $ret = $callNBP->invokeArgs($ld, array('CR1', '2020-01-01'));
        }
        catch(\Exception $e) {
            $this->assertEquals("Unknown currency CR1", $e->getMessage());
        }

        try {
            $ret = $callNBP->invokeArgs($ld, array('EUR', '2030-01-01'));
        }
        catch(\Exception $e) {
            $this->assertMatchesRegularExpression("/^NBP API error: 400 BadRequest/", $e->getMessage());
        }
    }

    public function test_fetching_from_tableA_and_existing_date()
    {
        // (1) boot the Symfony kernel
        self::bootKernel();
        // (2) use self::$container to access the service container
        $container = self::$container;

        // (3) run some service & test the result
        $ld = $container->get(ExchRateLoader::class);
        $callNBP = new \ReflectionMethod($ld, 'callNBP');
        $callNBP->setAccessible(true);

        $ret = $callNBP->invokeArgs($ld, array('EUR', '2021-06-14'));
        $this->assertEquals(4.5027, $ret);
 
        $ret = $callNBP->invokeArgs($ld, array('CLP', '2021-06-17'));
        $this->assertEquals(0.005194, $ret);
    }


    public function test_fetching_from_tableB_and_existing_date()
    {
        // (1) boot the Symfony kernel
        self::bootKernel();
        // (2) use self::$container to access the service container
        $container = self::$container;

        // (3) run some service & test the result
        $ld = $container->get(ExchRateLoader::class);
        $callNBP = new \ReflectionMethod($ld, 'callNBP');
        $callNBP->setAccessible(true);

        $ret = $callNBP->invokeArgs($ld, array('PAB', '2021-06-16'));
        $this->assertEquals(3.7336, $ret);
 
        $ret = $callNBP->invokeArgs($ld, array('VES', '2021-06-16'));
        $this->assertEquals(0.00000120, $ret);
    }

    public function test_fetching_from_tableA_and_non_existing_date()
    {
        // (1) boot the Symfony kernel
        self::bootKernel();
        // (2) use self::$container to access the service container
        $container = self::$container;

        // (3) run some service & test the result
        $ld = $container->get(ExchRateLoader::class);
        $callNBP = new \ReflectionMethod($ld, 'callNBP');
        $callNBP->setAccessible(true);

        $ret = $callNBP->invokeArgs($ld, array('EUR', '2021-05-01'));
        $this->assertEquals(4.5654, $ret);
 
        $ret = $callNBP->invokeArgs($ld, array('CLP', '2021-05-01'));
        $this->assertEquals(0.005321, $ret);
    }

    public function test_fetching_from_tableB_and_non_existing_date()
    {
        // (1) boot the Symfony kernel
        self::bootKernel();
        // (2) use self::$container to access the service container
        $container = self::$container;

        // (3) run some service & test the result
        $ld = $container->get(ExchRateLoader::class);
        $callNBP = new \ReflectionMethod($ld, 'callNBP');
        $callNBP->setAccessible(true);

        $ret = $callNBP->invokeArgs($ld, array('PAB', '2021-05-01'));
        $this->assertEquals(3.7939, $ret);
 
        $ret = $callNBP->invokeArgs($ld, array('VES', '2021-05-01'));
        $this->assertEquals(0.00000141, $ret);
    }
}