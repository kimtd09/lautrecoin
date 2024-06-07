<?php

namespace App\Service;
use App\Repository\DepartmentRepository;

class Departments {
    private $derpartments;
    public function __construct(DepartmentRepository $departmentRepository) {
        $this->derpartments = $departmentRepository->findAllSortedDepartments();
    }

    public function getAll() {
        return $this->derpartments;
    }
}