<?php

namespace App\Service;

use App\Repository\CategoryRepository;
use App\Repository\VendorRepository;

class TwigGlobalsService
{
    /** @var CategoryRepository */
    private $categoryRepository;

    /** @var VendorRepository */
    private $vendorRepository;


    /**
     * @param CategoryRepository $categoryRepository
     * @param VendorRepository $vendorRepository
     */
    public function __construct(CategoryRepository $categoryRepository, VendorRepository $vendorRepository)
    {
        $this->categoryRepository = $categoryRepository;
        $this->vendorRepository = $vendorRepository;
    }

    public function getMenuCategories()
    {
        return $this->categoryRepository->findAll();
    }

    public function getMenuVendors()
    {
        return $this->vendorRepository->findAll();
    }

}