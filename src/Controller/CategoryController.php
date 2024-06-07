<?php

namespace App\Controller;

use App\Entity\Product;
use App\Service\Categories;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

class CategoryController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    #[Route(path:'/category/{id}', name:'category_byid', defaults:['id'=> 0])]
    public function categoryById($id, Request $request) {


        $categoryPattern = "/^\b([4-9]|[1-6][0-9]|7[0-3])\b$/";

        if(!preg_match($categoryPattern, $id) && $id) {
            if(strlen($id)>10) {
                $id = substr($id, 0, 10)."...";
            }
            $this->addFlash("error", "error: unknown category $id");
            return $this->redirectToRoute('home');
        }

        $productRepo = $this->em->getRepository(Product::class);
        $page = $request->query->get('page',1);

        $found = $productRepo->countForThisCategory($id);
        if($found == null) {
            $this->addFlash("error", "error category: $id");
            return $this->redirectToRoute('home');
        }
        $max = ceil($found/12);

        // dd($page, $id);
        
        if( $page > $max ) {
            $this->addFlash("warning", "page overflow detected");
            $page = $max;
        }

        if($id == 0) {
            $products = $productRepo->findAll();
        } else {
            // $products = $productRepo->findBy(['category' => $id]);
            $products = $productRepo->findByCategoryWithPage($id, $page);
            if($products == null) {
                $this->addFlash("error", "error category: $id");
                return $this->redirectToRoute('home');
            }
        }
        
        $category = $id; // needed for homepage menu
        
        return $this->render('home.html.twig', compact('products','category', 'page', 'found', 'max'));
    }

    public function categoriesBar(Categories $categoriesService, $category=null) {
        $categories = $categoriesService->getAll();
        return $this->render('navbar/_categories.twig',compact('categories','category'));
    }

    public function getName(Categories $categoriesService, $categoryId=null) {
        $categoryName = $categoriesService->getName($categoryId);
        return $this->render('filter/_category.html.twig',compact('categoryName'));
    }

}
