<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HelloController extends AbstractController

{

    /**
     * @Route ("/hello/{firstname?World}", name="hello_name", methods={"GET"})
     */
    public function hello($firstname)
    {
        return new Response($this->render("hello.html.twig", [
            "prenom" => $firstname
        ]));
    }

    /**
     * @Route("/example",name="example")
     */
    public function example()
    {
        return new Response($this->render("exemple.html.twig", [
            'age' => 33
        ]));
    }
}
