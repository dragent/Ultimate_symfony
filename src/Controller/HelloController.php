<?php

namespace App\Controller;

use App\Taxes\Calculator;
use App\Taxes\Detector;
use Cocur\Slugify\Slugify;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;


class HelloController
{
    private LoggerInterface $logger;
    private Calculator $calculator;

    public function __construct(LoggerInterface $logger, Calculator $calculator)
    {
        $this->calculator = $calculator;
        $this->logger = $logger;
    }

    /**
     * @Route ("/hello/{firstname?World}", name="hello_name", methods={"GET"})
     */
    public function hello($firstname, Slugify $slugify, Detector $detector)
    {
        dump($detector->detect(101));
        dump($detector->detect(10));
        dump($slugify->slugify("Hello world"));
        $this->logger->info("mon message de log");
        $tva = $this->calculator->calcul(100);
        dump($tva);
        return new Response("Hello $firstname");
    }
}
