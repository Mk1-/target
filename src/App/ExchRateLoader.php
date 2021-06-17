<?php
namespace App\App;

use Doctrine\ORM\EntityManagerInterface;
use App\Entity\ExchangeRate;

class ExchRateLoader
{
    protected EntityManagerInterface $em;
    private $tableA;
    private $tableB;
    private $count;

    private const NBP_API_URL = "http://api.nbp.pl/api/exchangerates/rates/";

    public function __construct(EntityManagerInterface $entityManager)
    {
	    $this->em = $entityManager;
        $this->tableA = array_flip(
                        array('THB', 'USD', 'AUD', 'HKD', 'CAD', 'NZD', 'SGD', 'EUR', 'HUF', 'CHF', 'GBP', 'UAH', 'JPY','CZK',
                          'DKK', 'ISK', 'NOK', 'SEK', 'HRK', 'RON', 'BGN', 'TRY', 'ILS', 'CLP', 'PHP', 'MXN', 'ZAR', 'BRL', 'MYR',
                          'RUB', 'IDR', 'INR', 'KRW', 'CNY', 'XDR'));
        $this->tableB = array_flip(
                        array('AFN', 'MGA', 'PAB', 'ETB', 'VES', 'BOB', 'CRC', 'SVC', 'NIO', 'GMD', 'MKD', 'DZD', 'BHD', 'IQD',
                          'JOD', 'KWD', 'LYD', 'RSD', 'TND', 'MAD', 'AED', 'STN', 'BSD', 'BBD', 'BZD', 'BND', 'FJD', 'GYD', 'JMD',
                          'LRD', 'NAD', 'SRD', 'TTD', 'XCD', 'SBD', 'ZWL', 'VND', 'AMD', 'CVE', 'AWG', 'BIF', 'XOF', 'XAF', 'XPF',
                          'DJF', 'GNF', 'KMF', 'CDF', 'RWF', 'EGP', 'GIP', 'LBP', 'SSP', 'SDG', 'SYP', 'GHS', 'HTG', 'PYG', 'ANG',
                          'PGK', 'LAK', 'MWK', 'ZMW', 'AOA', 'MMK', 'GEL', 'MDL', 'ALL', 'HNL', 'SLL', 'SZL', 'LSL', 'AZN', 'MZN',
                          'NGN', 'ERN', 'TWD', 'TMT', 'MRU', 'TOP', 'MOP', 'ARS', 'DOP', 'COP', 'CUP', 'UYU', 'BWP', 'GTQ', 'IRR',
                          'YER', 'QAR', 'OMR', 'SAR', 'KHR', 'BYN', 'LKR', 'MVR', 'MUR', 'NPR', 'PKR', 'SCR', 'PEN', 'KGS', 'TJS',
                          'UZS', 'KES', 'SOS', 'TZS', 'UGX', 'BDT', 'WST', 'KZT', 'MNT', 'VUV', 'BAM'));
        $this->count = 0;
    }

    public function loadExchRates()
    {
        $conn = $this->em->getConnection();
        $sql = '
            SELECT DISTINCT i.date, i.currency
            FROM invoice i
            LEFT JOIN exchange_rate e ON i.date = e.date AND i.currency = e.currency
            WHERE e.date IS NULL
            UNION
            SELECT DISTINCT i.date, c.currency
            FROM ( SELECT id AS currency FROM currency ) c
            JOIN invoice i
            LEFT JOIN exchange_rate e ON i.date = e.date AND c.currency = e.currency
            WHERE e.date IS NULL';
        $TAB = $conn->executeQuery($sql)->fetchAllAssociative();
        foreach ( $TAB as $T ) {
            $rate = $this->callNBP($T['currency'], $T['date']);
            $obj = new ExchangeRate;
            $obj->setCurrency($T['currency']);
            $obj->setDate(new \DateTime($T['date']));
            $obj->setRateToPLN($rate);
            $this->em->persist($obj);
        }
        $this->em->flush();
    }

    private function callNBP(string $currency, string $date)
    {
        if ( $currency == "PLN" ) {
            return 1;
        }

        if ( (! isset($this->tableA[$currency])) &&  (! isset($this->tableB[$currency])) ) {
            throw new \Exception("Unknown currency " . $currency, 1);
        }

        if ( isset($this->tableA[$currency]) ) {
            $repeat = 10;
            $table = 'A';
        }
        else {
            $repeat = 20;
            $table = 'B';
        }
        // see https://api.nbp.pl/
        $url_c = self::NBP_API_URL . $table . '/' . $currency . '/';
        $dt = new \DateTime($date);
        $dti = new \DateInterval("P1D");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_HEADER, 0);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

        for ( $i = 1; $i <= $repeat; $i++ ) {
            $this->count++;
            if ( $this->count > 100 ) {
                // protection against 429 Too Many Requests
                sleep(3);
                $this->count = 0;
            }
            $url = $url_c . $dt->format('Y-m-d');
            curl_setopt($ch, CURLOPT_URL, $url);
            $ret = curl_exec($ch);
            if ( $ret === false ) {
                throw new \Exception("NBP API error: " . curl_error($ch), 1);
            }
            if ( substr($ret, 0, 3) == "404" ) {
                $dt->sub($dti);
                continue;
            }
            if ( substr($ret, 0, 3) == "400" ) {
                throw new \Exception("NBP API error: " . $ret, 2);
            }
            if ( substr($ret, 0, 1) == "{" ) {
                $obj = json_decode($ret);
                curl_close($ch);
                return $obj->rates[0]->mid;
            }
            throw new \Exception("NBP API error: " . $ret, 3);
        }
        throw new \Exception(sprintf("No rates for currency %s in %d days period down from date %s - very strange.", $currency, $repeat, $date), 10);
    }

}