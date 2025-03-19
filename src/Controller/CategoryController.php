<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\CategoryType;
use App\Form\ProductSearchType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

/**
 * @Route("/category")
 */
class CategoryController extends AbstractController
{

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/index", name="category_index", methods={"GET"})
     */
    public function index(CategoryRepository $categoryRepository): Response
    {
        return $this->render('category/index.html.twig', [
            'categories' => $categoryRepository->findAll()
        ]);
    }

    /**
     * @Route("/view/{category}", name="category_view")
     */
    public function view(Category $category, ProductRepository $productRepository, Request $request): Response
    {
        $form = $this->createForm(ProductSearchType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $qb = $productRepository->createQueryBuilder('p');

            if (!empty($data['price_range'])) {
                $values = explode('-', $data['price_range']);

                if (count($values) === 2) {
                    $qb->andWhere('p.price BETWEEN :start AND :end')
                        ->setParameter('start', $values[0])
                        ->setParameter('end', $values[1]);
                }
            }

            if ($data['category'] !== null) {
                $qb->andWhere('p.category = :category')
                    ->setParameter('category', $data['category']);
            }

            if ($data['vendor'] !== null) {
                $qb->andWhere('p.vendor = :vendor')
                    ->setParameter('vendor', $data['vendor']);
            }

            $products = $qb->getQuery()->getResult();
        } else {
            $products = $category->getProducts();
        }

        $form_submitted = $form->isSubmitted();

        return $this->render('category/show.html.twig', [
            'category' => $category,
            'products' => $products,
            'form_submitted' => $form_submitted,
            'form' => $form->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/create", name="category_form")
     */
    public function create(Request $request): Response
    {
        $category = new Category();
        $categoryForm = $this->createForm(CategoryType::class, $category);
        $categoryForm->handleRequest($request);
        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
        }

        return $this->render('category/create.html.twig', [
            'controller_name' => 'CategoryController',
            'categoryForm' => $categoryForm->createView()
        ]);
    }

    /**
     * @IsGranted("ROLE_ADMIN")
     * @Route("/{id}/edit", name="category_edit", methods={"GET", "POST"})
     */
    public function edit(Category $category, Request $request): Response
    {
        $categoryForm = $this->createForm(CategoryType::class, $category);
        $categoryForm->handleRequest($request);

        if ($categoryForm->isSubmitted() && $categoryForm->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();

            return $this->redirectToRoute('category_edit', ['id' => $category->getId()], Response::HTTP_SEE_OTHER);

        }

        return $this->render('category/edit.html.twig', [
            'categoryForm' => $categoryForm->createView()
        ]);
    }
}
