<?php

namespace App\Service;
use App\Repository\RegionRepository;

class Regions {

    private $regions;

    public function __construct(
        private RegionRepository $regionRepository,
        ) {
            $this->regions = $regionRepository->findAll();
    }

    public function getAll() {
        return $this->regions;
    }
}