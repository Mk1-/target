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

        $T = reset($RET);
        $HEAD = array_merge(array_slice(array_keys($T), 0, -1), array_keys($T['IN_CURRENCY']));
        return $this->render('showlist.html.twig', [
            'TABLE' => $RET, 'HEAD' => $HEAD
        ]);
    }
}
