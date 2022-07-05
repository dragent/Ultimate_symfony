<?php

namespace App\EventDispatcher;

use Psr\Log\LoggerInterface;
use App\Event\ProductViewEvent;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class ProductViewEmailSubscriber implements EventSubscriberInterface
{

    protected LoggerInterface $logger;
    protected MailerInterface $mailer;

    public function __construct(LoggerInterface $logger, MailerInterface $mailerInterface)
    {
        $this->logger = $logger;
        $this->mailer = $mailerInterface;
    }

    public static function getSubscribedEvents()
    {
        return ['product.view' => 'sendViewEmail'];
    }

    public function sendViewEmail(ProductViewEvent $productViewEvent)
    {
        $email = new TemplatedEmail();
        $email->from(new Address("admin@mail.com", "test"))
            ->to("admin@mail.com")
            ->text("Un visiteur est en train de voir la page du produit n°" . $productViewEvent->getProduct()->getId())
            ->htmlTemplate("emails/product_view.html.twig")
            ->context([
                "product" => $productViewEvent->getProduct(),
            ])
            ->subject("Visite du produit n°" . $productViewEvent->getProduct()->getId());
        $this->mailer->send($email);
    }
}
