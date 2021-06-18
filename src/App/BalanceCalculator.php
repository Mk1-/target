<?php
namespace App\App;

use Doctrine\ORM\EntityManagerInterface;
use App\App\CurrencyConverter;

class BalanceCalculator
{
    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
	    $this->em = $entityManager;
    }

    public function calculate($customer, $currency)
    {
        $conn = $this->em->getConnection();

        $sql = '
            SELECT i.id, i.value, i.currency, e.rate_to_PLN AS src_rate, e2.rate_to_PLN AS dst_rate
            FROM invoice i
            LEFT JOIN exchange_rate e ON i.date = e.date AND i.currency = e.currency
            LEFT JOIN exchange_rate e2 ON i.date = e2.date AND :currency = e2.currency
            WHERE i.sender = :customer
            ';
        $TAB = $conn->executeQuery($sql, ['customer' => $customer, 'currency' => $currency])->fetchAllAssociative();
        $sum_send = 0;
        foreach ( $TAB as $T ) {
            $sum_send += CurrencyConverter::convert_value($T['value'], $T['src_rate'], $T['dst_rate']);
        }

        $sql = '
            SELECT i.id, i.value, i.currency, e.rate_to_PLN AS src_rate, e2.rate_to_PLN AS dst_rate
            FROM invoice i
            LEFT JOIN exchange_rate e ON i.date = e.date AND i.currency = e.currency
            LEFT JOIN exchange_rate e2 ON i.date = e2.date AND :currency = e2.currency
            WHERE i.recipient = :customer
            ';
        $TAB = $conn->executeQuery($sql, ['customer' => $customer, 'currency' => $currency])->fetchAllAssociative();
        $sum_reci = 0;
        foreach ( $TAB as $T ) {
            $sum_reci += CurrencyConverter::convert_value($T['value'], $T['src_rate'], $T['dst_rate']);
        }

        $sum_send = round($sum_send, 4);
        $sum_reci = round($sum_reci, 4);
        return array('przychod' => $sum_send, 'koszt' => $sum_reci, 'dochod' => $sum_send - $sum_reci);
    }
}