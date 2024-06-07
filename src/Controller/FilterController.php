<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Service\Departments;
use App\Service\Regions;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class FilterController extends AbstractController
{
    #[Route('/filter', name: 'app_filter')]
    public function index(
        Request $request,
        ProductRepository $productRepository
        ): Response
    {
        $minPrice = $request->get('min_price');
        $maxPrice = $request->get('max_price');
        $regionId = $request->get('region_select');
        $departmentId = $request->get('department_select');
        $dateRange = $request->get('date_select');
        $category = $request->get('category');
        $keyword = $request->get('keyword');
        $page = $request->get('page', 1);

        // Regex check

        $priceRegex = "/^[\d]+$/";

        if(!preg_match($priceRegex, $minPrice) && $minPrice) {
            if(strlen($minPrice) > 10) {
                $minPrice = substr($minPrice, 0, 10)."...";
            }
            $this->addFlash("error", "error: invalid filter min price $minPrice");
            return $this->redirectToRoute('home');
        }

        if(!preg_match($priceRegex, $maxPrice) && $maxPrice) {
            if(strlen($maxPrice) > 10) {
                $maxPrice = substr($maxPrice, 0, 10)."...";
            }
            $this->addFlash("error", "error: invalid filter min price $maxPrice");
            return $this->redirectToRoute('home');
        }

        $regionPattern = "/^\b([1-9]|1[0-8])\b$/"; // 1 to 18

        if(!preg_match($regionPattern, $regionId) && $regionId) {
            if(strlen($regionId)>10) {
                $regionId = substr($regionId, 0, 10)."...";
            }
            $this->addFlash("error", "error: invalid filter for region $regionId");
            return $this->redirectToRoute('home');
        }

        $departmentPattern = "/^\b([1-9]|[1-9][0-9]|100|101)\b$/"; // 1 to 101

        if(!preg_match($departmentPattern, $departmentId) && $departmentId) {
            if(strlen($departmentId)>10) {
                $departmentId = substr($departmentId, 0, 10)."...";
            }
            $this->addFlash("error", "error: invalid filter for department $departmentId");
            return $this->redirectToRoute('home');
        }

        $datePattern = "/^\b(1|7|30)\b$/"; // 1, 7, 30

        if(!preg_match($datePattern, $dateRange) && $dateRange) {
            if(strlen($dateRange)>10) {
                $dateRange = substr($dateRange, 0, 10)."...";
            }
            $this->addFlash("error", "error: invalid filter for date $dateRange");
            return $this->redirectToRoute('home');
        }

        $categoryPattern = "/^\b([4-9]|[1-6][0-9]|7[0-3])\b$/";

        if(!preg_match($categoryPattern, $category) && $category) {
            if(strlen($dateRange)>10) {
                $category = substr($category, 0, 10)."...";
            }
            $this->addFlash("error", "error: unknown category $category");
            return $this->redirectToRoute('home');
        }

        $found = $productRepository->countForFilters($minPrice, $maxPrice, $regionId, $departmentId, $dateRange, $category, $keyword);

        $max = $found ? ceil($found/12) : 1;

        if($page > $max) {
            $this->addFlash('warning', "page overflow detected");
            $page = $max;
        }

        $products = $productRepository->findWithFilters($minPrice, $maxPrice, $regionId, $departmentId, $dateRange, $category, $keyword, $page);

        return $this->render('filter/index.html.twig', compact('products','found','page','max','category'));
    }

    public function regionList(Regions $regionsService, $selectedRegionId) {
        $regions = $regionsService->getAll();
        return $this->render('filter/_regions.html.twig',compact('regions','selectedRegionId'));
    }

    public function departmentList(Departments $departmentsService, $selectedDepartmentId) {
        $departments = $departmentsService->getAll();
        return $this->render('filter/_departments.html.twig',compact('departments','selectedDepartmentId'));
    }

    public function dateList($selectedDateId) {
        return $this->render('filter/_date.html.twig',compact('selectedDateId'));
    }
}
