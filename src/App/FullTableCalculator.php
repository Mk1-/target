<?php
namespace App\App;

use Doctrine\ORM\EntityManagerInterface;

use App\Entity\Currency;
use App\Entity\Invoice;
use App\Entity\Customer;
use App\Entity\ExchangeRate;
use App\App\CurrencyConverter;

class FullTableCalculator
{
    protected $em;

    public function __construct(EntityManagerInterface $entityManager)
    {
	    $this->em = $entityManager;
    }

    public function calculate()
    {
        $CURR = $this->em->getRepository(Currency::class)->findAll();
        $INV  = $this->em->getRepository(Invoice::class)->findAll();
        $CUST = $this->em->getRepository(Customer::class)->findAll();
        $TEMP = $this->em->getRepository(ExchangeRate::class)->findAll();
        $EXCH = array();
        foreach ( $TEMP as $rate ) {
            $EXCH[$rate->getCurrency()][$rate->getDate()->format('Y-m-d')] = $rate->getRateToPLN();
        }
        unset($TEMP);

        $RET = array();
        foreach ( $INV as $invoice ) {
            $ROW = array();
            $ROW['id'] = $invoice->getId();
            $ROW['date'] = $invoice->getDate()->format('Y-m-d');
            $ROW['currency'] = $invoice->getCurrency();
            $ROW['value'] = $invoice->getValue();
            $ROW['sender'] = ( isset($CUST[$invoice->getSender()]) ) ? $CUST[$invoice->getSender()]->getName() : "cst_id=" . $invoice->getSender();
            $ROW['recipient'] = ( isset($CUST[$invoice->getRecipient()]) ) ? $CUST[$invoice->getRecipient()]->getName() : "cst_id=" . $invoice->getRecipient();
            $ROW['IN_CURRENCY'] = array();
            foreach ( $CURR as $curr ) {
                $ROW['IN_CURRENCY'][$curr->getId()] = round(CurrencyConverter::convert($ROW['date'], $ROW['currency'], $curr->getId(), $ROW['value'], $EXCH), 4);
            }
            $RET[$ROW['id']] = $ROW;
        }

        return $RET;
    }

}