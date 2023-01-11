<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartRow;
use App\Entity\Dish;
use App\Repository\CartRepository;
use App\Repository\CategoryRepository;
use App\Repository\DishRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(EntityManagerInterface $em): Response
    {
        return $this->render('home/index.html.twig', [
            'controller_name' => 'HomeController',
        ]);
    }

    #[Route('/list', name: 'app_list')]
    public function list(DishRepository $dishRepository, CategoryRepository $categoryRepository): Response
    {
        return $this->render('home/list.html.twig', [
            'categories' => $categoryRepository->findBy([], ['name'=>'DESC']),
            'dishes' => $dishRepository->findAllWithCategories(false,'DESC')
        ]);
    }
}
