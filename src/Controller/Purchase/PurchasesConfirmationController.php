<?php

namespace App\Controller\Purchase;

use DateTime;
use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use App\Form\CartConfirmationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PurchasesConfirmationController extends AbstractController
{

    protected $cartService;
    protected $em;

    public function __construct(CartService $cartService, EntityManagerInterface $em)
    {
        $this->cartService = $cartService;
        $this->em = $em;
    }
    /**
     * @Route("/commande/confirmation", name="purchase_confirm")
     * IsGranted("ROLE_USER",message="Vous devez être connecté pour valider une commande")
     */
    public function confirm(Request $request)
    {
        $form = $this->createForm(CartConfirmationType::class);
        $form->handleRequest($request);
        if (!$form->isSubmitted()) {
            $this->addFlash('warning', 'vous devez remplir le formulaire de construction');
            return $this->redirectToRoute('cart_show');
        }

        $user = $this->getUser();

        $cartItems = $this->cartService->getDetailedCartItems();
        if (count($cartItems) === 0) {
            $this->addFlash('warning', 'vous ne pouvez pas valider un panier vide');
            return $this->redirectToRoute('cart_show');
        }

        /** @var Purchase */
        $purchase = $form->getData();
        $purchase->setPurchasedAt(new DateTime())
            ->setUser($user)
            ->setTotal($this->cartService->getTotal());
        $this->em->persist($purchase);
        foreach ($cartItems as $cartItem) {
            $purchaseItem = new PurchaseItem();
            $purchaseItem->setPurchase($purchase)
                ->setProduct($cartItem->product)
                ->setProductName($cartItem->product->getName())
                ->setProductPrice($cartItem->product->getPrice())
                ->setTotal($cartItem->getTotal())
                ->setQuantity($cartItem->qty);
            $this->em->persist($purchaseItem);
        }
        $this->cartService->empty();
        $this->em->flush();
        $this->addFlash("success", "La commande a bien été enregistrée");
        return $this->redirectToRoute('purchase_index');
    }
}
