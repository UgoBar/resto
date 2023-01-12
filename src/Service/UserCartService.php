<?php

namespace App\Service;

use App\Entity\Cart;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

class UserCartService
{

    public function __construct(
        private EntityManagerInterface $em
    ) {}

    public function getOrCreateCurrentCart(?User $user): ?Cart
    {

        if($user === null)
            return null;

        $currentCart = $this->em->getRepository(Cart::class)->findOneBy([
            'user' => $user,
            'state' => false,
        ]);

        if ($currentCart === null) {

            $newCart = new Cart();
            $newCart->setState(false);
            $newCart->setTotalPrice(0);
            $newCart->setUpdatedAt(new \DateTime());
            $newCart->setUser($user);
            $newCart->setCreatedAt(new \DateTime());

            $currentCart = $newCart;

            $this->em->persist($currentCart);
            $this->em->flush();
        }

        return $currentCart;
    }

}
