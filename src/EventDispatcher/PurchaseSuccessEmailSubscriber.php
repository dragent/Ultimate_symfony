<?php

namespace App\EventDispatcher;

use App\Entity\User;
use Psr\Log\LoggerInterface;
use App\Event\PurchaseSuccessEvent;
use Symfony\Component\Mime\Address;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class PurchaseSuccessEmailSubscriber implements EventSubscriberInterface
{
    protected LoggerInterface $logger;
    protected MailerInterface $mailer;
    protected Security $security;

    public function __construct(LoggerInterface $logger, MailerInterface $mailerInterface, Security $security)
    {
        $this->logger = $logger;
        $this->mailer = $mailerInterface;
        $this->security = $security;
    }

    public static function  getSubscribedEvents()
    {
        return ['purchase.success' => 'sendSuccessEmail'];
    }

    public function sendSuccessEmail(PurchaseSuccessEvent $purchaseSuccessEvent)
    {
        /** @var User */
        $user = $this->security->getUser();
        $purchase = $purchaseSuccessEvent->getPurchase();
        $email = new TemplatedEmail();
        $email->to(new Address($user->getEmail(), $user->getFullName()))
            ->from("contact@mail.com")
            ->subject("Bravo Votre commande ({$purchase->getId()}) a bien été confirmée")
            ->htmlTemplate("emails/purchase_success.html.twig")
            ->context([
                "purchase" => $purchase,
                "user" => $user
            ]);
        $this->mailer->send($email);
    }
}
