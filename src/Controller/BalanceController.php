<?php

namespace App\Controller;

use App\Entity\Currency;
use App\Entity\Customer;
use App\App\BalanceCalculator;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Doctrine\ORM\EntityManagerInterface;

class BalanceController extends AbstractController
{
    /**
     * @Route("/balance", name="balance")
     */
    public function index(Request $request, EntityManagerInterface $em, BalanceCalculator $balCalc): Response
    {
        $CUST = $em->getRepository(Customer::class)->findAll();
        $CURR = $em->getRepository(Currency::class)->findAll();

        $form = $this->createFormBuilder()
        ->add('customer', ChoiceType::class, ['choices' => $CUST, 'choice_value' => 'id', 'choice_label' => 'name'])
        ->add('currency', ChoiceType::class, ['choices' => $CURR, 'choice_value' => 'id', 'choice_label' => 'id'])
        ->add('save', SubmitType::class, ['label' => 'Calculate'])
        ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $FR = $form->getData();
            $RET = $balCalc->calculate($FR['customer']->getId(), $FR['currency']->getId());
        }
        else {
            $RET = null;
        }

        return $this->render('balance.html.twig', [
            'form' => $form->createView(), 
            'RET' => $RET,
        ]);
    }
}
