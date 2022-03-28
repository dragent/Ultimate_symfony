<?php

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\String\Slugger\SluggerInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category")
     */
    public function category($slug, CategoryRepository $categoryRepository): Response
    {
        $category = $categoryRepository->findOneBy(["slug" => $slug]);
        if (!$category) {
            throw $this->createNotFoundException("La catégorie demandée n'existe pas");
        }
        return $this->render('product/category.html.twig', [
            'slug' => $slug,
            'category' => $category
        ]);
    }

    /**
     * @Route("/{category_slug}/{product_slug}", name="product_show")
     */
    public function show($product_slug, ProductRepository $productRepository)
    {
        $product = $productRepository->findOneBy(["slug" => $product_slug]);
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
    public function create(FormFactoryInterface $factory, Request $request, SluggerInterface $slugger, EntityManager $em)
    {
        $builder = $factory->createBuilder(FormType::class, null, [
            'data_class' => Product::class
        ]);

        $builder->add("name", TextType::class, [
            "label" => "Nom du produit",
            "attr" => [
                "placeholder" => "Tapez le nom du produit"
            ],
            "required" => true,
        ])
            ->add("shortDescription", TextareaType::class, [
                "label" => "Description courte",
                "attr" => [
                    "placeholder" => "Tapez une description assez courte mais parlante pour le visiteur"
                ],
                "required" => true
            ])
            ->add("price", MoneyType::class, [
                "label" => "Prix du produit",
                "attr" => [
                    "placeholder" => "Tapez le prix du produit en euro"
                ],
                "required" => true
            ])
            ->add("mainPicture", UrlType::class, [
                "label" => "image du produit",
                "attr" => [
                    "placeholder" => "Tapez l'url d'une image"
                ]
            ])
            ->add("category", EntityType::class, [
                "label" => "Catégorie",
                "placeholder" => "-- Choisir une catégorie",
                "class" => Category::class,
                "choice_label" => function (Category $category) {
                    return ucfirst($category->getName());
                }

            ]);

        $form = $builder->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted()) {

            $product = $form->getData();
            $product->setSlug(strtolower($slugger->slug($product->getName())));
            $em->persist($product);
            $em->flush();
        }
        $formView = $form->createView();
        return $this->render('product/create.html.twig', [
            "formView" => $formView
        ]);
    }
}
