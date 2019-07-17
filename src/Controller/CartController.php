<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\User;
use App\Repository\CartRepository;
use App\Repository\TicketRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(name="cart_")
 */
class CartController extends AbstractController
{
    /**
     * @Route("/panier", name="index")
     */
    public function cart(Session $session, TicketRepository $ticketRepository)
    {
        foreach ($session->get('cart') as $idTicket => $qty) {
            $allTickets[] =  [ 'qty' => $qty, 'ticket' => $ticketRepository->find($idTicket)];
        }

        return $this->render('circus/cart/index.html.twig', ['allTickets' => $allTickets]);
    }

    /**
     * @Route("/validation-commande", name="validation")
     */
    public function cartValidation(Session $session,TicketRepository $ticketRepository, ObjectManager $manager)
    {
        $order = $session->get('cart');
        $cart = new Cart();

        $cart->setUser($this->getUser());

        foreach ($order as $idTicket => $qty) {
            $cart->addTicket($ticketRepository->find($idTicket));
            $cart->setQuantity($qty);
        }

        $cart->setDate(new \DateTime());

        $manager->persist($cart);
        $manager->flush();

        $this->addFlash('success', 'Merci pour votre achat');

        return $this->redirectToRoute('circus_home');
    }
}