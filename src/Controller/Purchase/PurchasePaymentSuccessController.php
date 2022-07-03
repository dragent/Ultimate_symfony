<?php

namespace App\Controller\Purchase;

use Error;
use Stripe\Stripe;
use App\Entity\Purchase;
use App\Cart\CartService;
use Stripe\PaymentIntent;
use App\Repository\PurchaseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class PurchasePaymentSuccessController extends AbstractController
{

    /**
     * @Route("/commande/paiment/check/{id}", name="purchase_payment_check")
     */
    public function confirm(int $id, PurchaseRepository $purchaseRepository)
    {
        Stripe::setApiKey('sk_test_51KxBiALVKfR0y6C4drUFYHZHHzlsmQRyqZDQp92uVIpI0Xn6AFxxzFavWkDgB8NRvEzZK3mZK1mOglEkIk6MZII700i2M5RntJ');
        $purchase = $purchaseRepository->find($id);
        if (
            !$purchase ||
            ($purchase->getUser() !== $this->getUser()) ||
            ($purchase->getStatus() === Purchase::STATUS_PAID)
        ) {
            $this->addFlash('warning', "La commande n'existe pas");
            return $this->redirectToRoute("purchase_index");
        }
        $response = new Response();
        $response->headers->set('Content-Type', 'application/json');
        try {
            // retrieve JSON from POST body
            $jsonStr = file_get_contents('php://input');
            $jsonObj = json_decode($jsonStr);
            // Create a PaymentIntent with amount and currency
            $paymentIntent = PaymentIntent::create([
                'amount' => $purchase->getTotal(),
                'currency' => 'eur',
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);

            $output = [
                'clientSecret' => $paymentIntent->client_secret,
            ];
            $response->setContent(json_encode($output));
        } catch (Error $e) {
            http_response_code(500);
            $response->setContent(json_encode(['error' => $e->getMessage()]));
        }
        return $response;
    }

    /**
     * @Route("/purchase/terminate/{id}", name="purchase_payment_success")
     * IsGranted("ROLE_USER",message="Vous devez être connecté pour valider une commande")
     */
    public function success(int $id, PurchaseRepository $purchaseRepository, EntityManagerInterface $em, CartService $cartService)
    {
        $purchase = $purchaseRepository->find($id);
        if (
            !$purchase ||
            ($purchase->getUser() !== $this->getUser()) ||
            ($purchase->getStatus() === Purchase::STATUS_PAID)
        ) {
            $this->addFlash('warning', "La commande n'existe pas");
            return $this->redirectToRoute("purchase_index");
        }
        $purchase->setStatus(Purchase::STATUS_PAID);
        $em->flush();
        $cartService->empty();
        $this->addFlash('success', 'La commande a été payée et confirmée');
        return $this->redirectToRoute("purchase_index");
    }
}
