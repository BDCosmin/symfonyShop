<?php

namespace App\Controller;

use App\Entity\Vendor;
use App\Form\Vendor1Type;
use App\Form\VendorType;
use App\Repository\VendorRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/vendor")
 */
class VendorController extends AbstractController
{
    /**
     * @Route("/", name="app_vendor_index", methods={"GET"})
     */
    public function index(VendorRepository $vendorRepository): Response
    {
        return $this->render('vendor/index.html.twig', [
            'vendors' => $vendorRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="app_vendor_new", methods={"GET", "POST"})
     */
    public function new(Request $request, VendorRepository $vendorRepository): Response
    {
        $vendor = new Vendor();
        $form = $this->createForm(VendorType::class, $vendor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vendorRepository->add($vendor);
            return $this->redirectToRoute('app_vendor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vendor/new.html.twig', [
            'vendor' => $vendor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_vendor_show", methods={"GET"})
     */
    public function show(Vendor $vendor): Response
    {

        $products = $vendor->getProducts();
        return $this->render('vendor/show.html.twig', [
            'vendor' => $vendor,
            'products' => $products
        ]);
    }

    /**
     * @Route("/{id}/edit", name="app_vendor_edit", methods={"GET", "POST"})
     */
    public function edit(Request $request, Vendor $vendor, VendorRepository $vendorRepository): Response
    {
        $form = $this->createForm(VendorType::class, $vendor);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $vendorRepository->add($vendor);
            return $this->redirectToRoute('app_vendor_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('vendor/edit.html.twig', [
            'vendor' => $vendor,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="app_vendor_delete", methods={"POST"})
     */
    public function delete(Request $request, Vendor $vendor, VendorRepository $vendorRepository): Response
    {
        if ($this->isCsrfTokenValid('delete'.$vendor->getId(), $request->request->get('_token'))) {
            $vendorRepository->remove($vendor);
        }

        return $this->redirectToRoute('app_vendor_index', [], Response::HTTP_SEE_OTHER);
    }
}
