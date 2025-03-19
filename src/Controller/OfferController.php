<?php

namespace App\Controller;

use App\Entity\Product;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OfferController extends AbstractController
{
    /**
     * @Route("/offer", name="offer")
     */
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findBy(['offerStatus' => 1]);

        return $this->render('default/offers.html.twig', [
            'products' => $products
        ]);
    }

}
