<?php

namespace App\Controller;

use App\Form\LoginType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class SecurityController extends AbstractController
{
    /**
     * @Route("/connexion", name="security_login")
     */
    public function login(AuthenticationUtils $utils, FormFactoryInterface $factory): Response
    {
        $form = $this->createForm(LoginType::class, ['email' => $utils->getLastUsername()]);
        //$form = $factory->createNamed('', LoginType::class, ['email' => $utils->getLastUsername()]);
        return $this->render(
            'security/login.html.twig',
            [
                "formView" => $form->createView(),
                "error" => $utils->getLastAuthenticationError(),
            ]
        );
    }

    /**
     * @Route("/deconnexion", name="security_logout")
     */
    public function logout()
    {
    }
}
