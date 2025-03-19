<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\ProductSearchType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class DefaultController extends AbstractController
{

    /**
     * @Route("/", name="homepage")
     */
    public function index(): Response
    {

        return $this->render('default/index.html.twig');
    }

    /**
     * @Route("/search-results", name="search_results")
     */
    public function search(CategoryRepository $categoryRepository, ProductRepository $productRepository, Request $request): Response
    {

        $searchInput = $request->query->get('searchInput');
        $categoryId = $request->query->get('category');

        $category = null;
        if ($categoryId) {
            $category = $categoryRepository->find($categoryId);
        }

        $qb = $productRepository->createQueryBuilder('p');

        if ($searchInput) {
            $qb->andWhere('p.name LIKE :name')
                ->setParameter('name', '%' . $searchInput . '%');
        }

        if ($category) {
            $qb->andWhere('p.category = :category')
                ->setParameter('category', $category);
        }

        $form = $this->createForm(ProductSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            if (!empty($data['price_range'])) {
                $values = explode('-', $data['price_range']);
                if (count($values) === 2) {
                    $qb->andWhere('p.price BETWEEN :start AND :end')
                        ->setParameter('start', $values[0])
                        ->setParameter('end', $values[1]);
                }
            }

            if ($data['category']) {
                $qb->andWhere('p.category = :category')
                    ->setParameter('category', $data['category']);
            }

            if ($data['vendor']) {
                $qb->andWhere('p.vendor = :vendor')
                    ->setParameter('vendor', $data['vendor']);
            }
        }

        $products = $qb->getQuery()->getResult();

        return $this->render('default/search_results.html.twig', [
            'products' => $products,
            'searchInput' => $searchInput,
            'category' => $category,
            'form' => $form->createView()
        ]);
    }

}
