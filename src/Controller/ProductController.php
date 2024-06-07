<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Department;
use App\Entity\Image;
use App\Entity\Product;
use App\Entity\Region;
use App\Entity\SourceUser;
use App\Form\CategoryType;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use App\Repository\SourceUserRepository;
use App\Scraper;
use App\Service\Counter;
use App\Service\Departments;
use App\Service\Pagination;
use App\Service\Regions;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Log\Logger;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em) {
        $this->em = $em;
    }

    #[Route('/product/add', 
    condition: "request.server.get('APP_ENV') matches '/dev/i'",
    name:'product.add', methods: ['GET','POST'])]
    public function add(Request $request): Response {
        $product = new Product();

        $list_id = $request->get('list_id');
        $title = $request->get('title');

        // dd($_GET);

        if($title) {
            $product->setListId($list_id);
            $product->setTitle($title);
            $product->setDescription($request->get('description'));
            $product->setPrice($request->get('price'));
            $product->setUrl($request->get('url'));
            $product->setZipcode($request->get('zipcode'));

            $userInRepo = $this->em->getRepository(SourceUser::class)->findOneBy(['userid' => $request->get('userid')]);
            if($userInRepo == null || $this->em->find(SourceUser::class, $userInRepo)?->getId() == null ) {
                $user = new SourceUser();
                $user->setUserSourceId($request->get('userid'));
                $userInRepo = $user;
                $this->em->persist($user);
                $this->em->flush();
            }
            $product->setSourceUser($userInRepo);

            // **NOT** stored in SourceUser entity
            $product->setUsername($request->get('username'));

            // publish date
            $product->setPublishDate((new \DateTimeImmutable($request->get('date'), new \DateTimeZone('Europe/Paris'))));

            // category through manager
            $category = $this->em->find(Category::class, $this->em->getRepository(Category::class)->findOneBy(['name' => $request->get('category')])->getId());
            $product->setCategory($category);

            // region through manager
            $region = $this->em->find(Region::class, $this->em->getRepository(Region::class)->findOneBy(['name' => $request->get('region')])->getId());
            $product->setRegion($region);

            // department through manager
            $department = $this->em->find(Department::class, $this->em->getRepository(Department::class)->findOneBy(['name' => $request->get('department')])->getId());
            $product->setDepartment($department);

            // city
            $product->setCity($request->get('city'));

            // each images added to the collection
            $product->setImageNumber($request->get('image_number'));
            if($product->getImageNumber()>0) {
                $product->setImageSmallUrl($request->get('image_url'));
                foreach( $request->get('image_urls') as $url ) {
                    $image = new Image();
                    $image->setUrl($url);
                    $product->addImage($image);
                    $this->em->persist($image);
                }
            }
        }

        $form = $this->createForm(ProductType::class, $product, $options = ['button_label' => 'add a product']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->em->persist($product);
            $this->em->flush();

            return $this->redirectToRoute('home', ['listid'=> $product->getListId()]); // using route param
            // return $this->redirectToRoute('home'); // use of flash message

        }

        return $this->render('product/add.html.twig',['form' => $form->createView(), 'product' => $product, 'image_urls' => $request->get('image_urls')]);
    }


    #[Route(path:'/get/{id}', name:'product.get', methods: ['GET'])]
    public function get(Request $request, int $id, ProductRepository $productRepository): Response {
        $product = $productRepository->find($id);

        if ($product == null) {
            $this->addFlash("error", "error: product $id not found");
            return $this->redirectToRoute("home");
        }

        return $this->render('product/get.html.twig', compact('product'));
    }

    #[Route(path:'/product/delete/{id}', 
    condition: "request.server.get('APP_ENV') matches '/dev/i'",
    name:'product.delete')]
    public function delete(Request $request, int $id): Response {

        $this->denyAccessUnlessGranted("ROLE_ADMIN");

        $product = $this->em->getRepository(Product::class)->find($id);
        if (!$product) {
            // return $this->redirectToRoute('home.error', ['errormessage'=> "product #$id not found"]);

            return $this->render('error.html.twig', ['errormessage'=> "product #$id not found"], new Response("not found",404));
        }
        $this->em->remove($product);
        $this->em->flush();
        $this->addFlash('success','');
        return $this->redirectToRoute('home');
    }


    #[Route('/sourceuser/{id}', name:'app_sourceuser', methods: ['GET'])]
    public function getSourceUser($id, ProductRepository $repository, SourceUserRepository $sourceUserRepository) {
        $products = $repository->findBy(array('sourceUser'=> $id));
        if( $products == null) {
            $this->addFlash('error', 'error: unknown source user');
            return $this->redirectToRoute('home');
        }
        return $this->render('product/sourceuser.html.twig',compact('products'));
    }

    // #[Route('/search/{keyword}', requirements: ["keyword" => "\w+"]  ,name:'app_product_search', methods: ['GET'])]
    #[Route('/search', name:'app_product_search', methods: ['GET'])]
    public function search(
        Request $request, 
        LoggerInterface $logger,
        ProductRepository $productRepository) : Response {
        try{
            $keyword = $request->get('keyword');
            $page = $request->get('page', 1);
            $found = $productRepository->countByKeyword($keyword);
            $max = $found ? ceil($found/12) : 1;

            if($page > $max) {
                $this->addFlash('warning', "page overflow detected");
                $page = $max;
            }

            $products = $productRepository->findByKeyword($keyword, $page);
            }
            catch  ( Exception $e) {
                $logger->log('error', $e->getMessage());
                return $this->redirectToRoute('home');
            }
        return $this->render('filter/index.html.twig', compact('products', 'found', 'page', 'max'));
    }

    public function totalCount(Counter $counter,) {
        $total = $counter->getTotal();
        return $this->render('partials/_total.html.twig',compact('total'));
    }
}
