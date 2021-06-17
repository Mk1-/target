<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use App\App\FullTableCalculator;
use App\View\FullTableCSV;

class ShowListController extends AbstractController
{
    /**
     * @Route("/showlist/{mode}", name="show_list", defaults={"mode"="html"})
     */
    public function index($mode, FullTableCalculator $ftCalc): Response
    {
        $RET = $ftCalc->calculate();

        if ( $mode == "json" ) {
            return new JsonResponse($RET);
        }

        if ( $mode == "csv" ) {
            $response = new Response(FullTableCSV::create($RET));
            $response->headers->set('Content-Type', 'text/csv');
            $response->headers->set('Content-Disposition', 'attachment; filename="fulltable.csv"');
            return $response;
        }

        return $this->render('showlist.html.twig', [
            'controller_name' => 'ShowListController', 'TABLE' => $RET
        ]);
    }
}
