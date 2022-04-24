<?php

namespace App\Controller\Purchase;

use App\Entity\User;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class PurchasesListController extends AbstractController
{

    /**
     * @Route("/commande", name="purchase_index")
     * @IsGranted("ROLE_USER", message ="vous devez être connecté")
     */
    public function index(): Response
    {
        /** @var User*/
        $user = $this->getUser();
        if (!$user) {
            throw new AccessDeniedException("Vous devez être connecté pour accéder aux commandes");
        }
        return $this->render('purchase/purchases_list/index.html.twig', [
            'purchases' => $user->getPurchases(),
        ]);
    }
}
