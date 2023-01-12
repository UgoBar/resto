<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartRow;
use App\Entity\Dish;
use App\Repository\CartRepository;
use App\Repository\CartRowRepository;
use App\Repository\CategoryRepository;
use App\Repository\DishRepository;
use App\Service\GetAgeService;
use App\Service\UserCartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/list', name: 'app_list')]
    public function list(DishRepository $dishRepository, CategoryRepository $categoryRepository, CartRowRepository $cartRowRepository, UserCartService $userCartService): Response
    {
        if($user = $this->getUser()) {
            $cartPending = $cartRowRepository->findPendingUserCart($user);
        }

        return $this->render('home/list.html.twig', [
            'categories' => $categoryRepository->findBy([], ['name'=>'DESC']),
            'dishes' => $dishRepository->findAllWithCategories(false,'DESC'),
            'cartPending' => $cartPending ?? null
        ]);
    }

    #[Route('/command-cart', name: 'command_cart')]
    public function commandCartAjax(EntityManagerInterface $em, Request $request, UserCartService $cartService): Response
    {
        $data = $this->getDataAndCheckUser($request);
        $cart = $cartService->getOrCreateCurrentCart($this->getUser());
        if(!$cart) {
            return new JsonResponse(['error' => true]);
        }

        $cart->setState(Cart::FINALISED);
        $em->persist($cart);
        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/remove-from-cart', name: 'remove_from_cart')]
    public function removeFromCartAjax(EntityManagerInterface $em, Request $request, CartRowRepository $cartRowRepository): Response
    {
        $data = $this->getDataAndCheckUser($request);

        $rowData = $data['row'];

        $row  = $cartRowRepository->find($rowData['rowId']);
        /** @var Cart $cart */
        $cart = $em->getRepository(Cart::class)->find($row->getCart()->getId());

        if($data['deleteRow'] === false) {
            // Row quantity - 1 AND calculate total cart
            $row->setQuantity($row->getQuantity() - 1);
            $cart->setTotalPrice($cart->getTotalPrice() - $row->getUnitPrice());
            $em->persist($row);
            $em->persist($cart);
        } else {
            $cart->setTotalPrice($cart->getTotalPrice() - $row->getUnitPrice());
            $em->remove($row);
            if($cart->getTotalPrice() == 0) {
                $em->remove($cart);
            }
        }

        $em->flush();

        return new JsonResponse(['success' => true]);
    }

    #[Route('/add-to-cart', name: 'add_to_cart')]
    public function addToCartAjax(EntityManagerInterface $em, Request $request, UserCartService $cartService, CartRowRepository $cartRowRepository): Response
    {

        $data = $this->getDataAndCheckUser($request);

        $user = $this->getUser();
        $rowData = $data['row'];

        $cart = $cartService->getOrCreateCurrentCart($user);
        $dish = $em->getRepository(Dish::class)->find($rowData['id']);

        if($cart) {
            $cartRow = new CartRow();
            $cartRow->setCart($cart)
                ->setUnitPrice($rowData['price'])
                ->setQuantity(1)
                ->setDish($dish)
            ;

            $createdRowId = $cartRowRepository->insertOrUpdateDuplicateRow($cartRow);

            $totalPrice = $cart->getTotalPrice() + $rowData['price'];
            $cart->setTotalPrice($totalPrice);
            $em->persist($cart);
            $em->flush();

            return new JsonResponse(['success' => true, 'rowId' => $createdRowId]);
        }

        return new JsonResponse(['error' => true]);
    }

    private function getDataAndCheckUser(Request $request)
    {
        $data = $request->getContent();
        $data = json_decode($data, true);

        if(empty($data) || !$this->getUser()) {
            return new JsonResponse(['error' => true]);
        }

        return $data;
    }
}
