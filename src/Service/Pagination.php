<?php

namespace App\Service;
use App\Repository\ProductRepository;

class Pagination {
    public function __construct(
        private ProductRepository $productRepository,
    ) {}

    public function findPage($page) {
        return $this->productRepository->findPage($page);
    }
}