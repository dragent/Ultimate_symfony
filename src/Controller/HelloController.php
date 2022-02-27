<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;

class HelloController
{



    /**
     * @Route ("/hello/{firstname?World}", name="hello_name", methods={"GET"})
     */
    public function hello($firstname, Environment $twig)
    {
        $html = $twig->render("hello.html.twig", [
            "prenom" => $firstname,
            "formateur1" =>  [
                "prenom" => "lior",
                "nom" => "chamla",
                "age" => 33
            ],
            "formateur2" =>  [
                "prenom" => "denise",
                "nom" => "test",
                "age" => 3
            ],
        ]);
        return new Response($html);
    }
}
