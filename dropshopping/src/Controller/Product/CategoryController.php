<?php

namespace App\Controller\Product;

use App\Repository\CategoriesRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends AbstractController
{
    public $categoryRepository;

    public function __construct( CategoriesRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }

    public function list()
    {
        $categories = $this->categoryRepository->findAll();
        $data = [];
        foreach ($categories as $category) {
            $data[] = [
                'id' => $category->getId(),
                'name' => $category->getName()
            ];
        }
        $response = new Response();
        $response->setContent(json_encode($data));
        return $response;
    }

}