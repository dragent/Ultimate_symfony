<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Event\ProductViewEvent;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{


    /**
     * @Route("/{category_slug}/{product_slug}", name="product_show", priority=-1)
     */
    public function show($product_slug, ProductRepository $productRepository, EventDispatcherInterface $dispatcher)
    {
        $product = $productRepository->findOneBy(["slug" => $product_slug]);
        $productEvent = new ProductViewEvent($product);
        $dispatcher->dispatch($productEvent, 'product.view');
        if (!$product) {
            throw $this->createNotFoundException("Le produit demandée n'existe pas");
        }
        return $this->render(
            "product/show.html.twig",
            [
                "product" => $product
            ]
        );
    }

    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em)
    {
        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->persist($product);
            $em->flush();
            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'product_slug' => $product->getSlug()
            ]);
        }
        $formView = $form->createView();
        return $this->render('product/create.html.twig', [
            "formView" => $formView
        ]);
    }

    /** 
     * @Route("/admin/product/{id}/edit", name="product_edit")
     */
    public function edit(
        int $id,
        ProductRepository $productRepository,
        Request $request,
        EntityManagerInterface $em,
        SluggerInterface $slugger
    ) {
        $product = $productRepository->find($id);
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->flush();
            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'product_slug' => $product->getSlug()
            ]);
        }
        $formView = $form->createView();
        return $this->render('product/edit.html.twig', [
            "product" => $product,
            "formView" => $formView
        ]);
    }
}
