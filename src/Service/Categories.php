<?php

namespace App\Service;
use App\Entity\Category;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;

class Categories {
    private $sortedCategories;
    private $categories;
    public function __construct(
        private CategoryRepository $categoryRepository,
    private EntityManagerInterface $entityManagerInterface,) {
        $this->sortedCategories = $categoryRepository->getAllSortedCategories();
        
        $array = $categoryRepository->findAll();
        foreach ($array as $row) {
                $this->categories[$row->getId()] = $row->getName();
        }
    }

    public function getAll() {
        return $this->sortedCategories;
    }

    public function getName($id) {
        return $this->categories[$id];
    }

    public function initDB() {
        // dd($_ENV);
        $buffer = file_get_contents($_ENV["PWD"]."/src/data/categories.json");
        $json = json_decode($buffer,true);

        foreach( $json as $name => $path ) {
            $category = new Category();
            $category->setName($name);
            $category->setPath($path);
            $this->entityManagerInterface->persist($category);
        }

        $this->entityManagerInterface->flush(); 
    }

    public function updateDB() {
        // dd($_ENV);
        $buffer = file_get_contents($_ENV["PWD"]."/src/data/categories.json");
        $json = json_decode($buffer,true);

        // returns [ ["name" => aaa], ["name" => abc], ... ]
        $categoriesInDb = $this->categoryRepository->getAllNames();

        $array = [];
        foreach ($categoriesInDb as $value) {
            // $array = [aaa, abc, ...]
            $array [] = $value["name"];
        }
        
        foreach( $json as $name => $path ) {
            $category = new Category();
            $category->setName($name);
            $category->setPath($path);
            if( in_array($name, $array) ) { continue; }

            $this->entityManagerInterface->persist($category);
        }

        $this->entityManagerInterface->flush(); 

        // controle
        // $categories = $this->categoryRepository->findAll();
        // dd($categories);
    }
}