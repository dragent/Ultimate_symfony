<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TestController extends AbstractController
{
    public function index(): Response
    {
        dd("Ca fonctionne");
    }

    /**
     * @Route(
     *  "/test/{age<\d+>?0", 
     *  name="app_test", 
     *  methods={"GET","POST"},
     *  host="localhost",
     *  schemes={"http","https"}
     * )
     */
    public function test(Request $request, $age)
    {
        return new Response("Vous avez $age ans!");
    }
}
