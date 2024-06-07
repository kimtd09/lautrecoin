<?php

namespace App\Service;
use App\Repository\ProductRepository;

class Counter {

    public function __construct(
        private ProductRepository $productRepository,
        ){}

    public function getTotal() : int {
        return $this->productRepository->count();
    }

    public function getMaxPage() : int {
        return ceil($this->getTotal()/12);
    }
}