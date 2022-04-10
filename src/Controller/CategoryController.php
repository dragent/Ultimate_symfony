<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CategoryController extends AbstractController
{

    protected $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }
    /**
     * @Route("/admin/category/create", name="category_create")
     */
    public function create(Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $em->persist($category);
            $em->flush();
            return $this->redirectToRoute('homepage');
        }
        $formView = $form->createView();
        return $this->render('category/create.html.twig', [
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/category/{id}/edit", name="category_edit")
     */
    public function edit(int $id, CategoryRepository $categoryRepository, Request $request, SluggerInterface $slugger, EntityManagerInterface $em): Response
    {
        // $user = $security->getUser();
        // if ($user === null) {
        //     return $this->redirectToRoute("security_login");
        // }
        // if (!in_array("ROLE_ADMIN", $user->getRoles())) {
        //     throw new AccessDeniedHttpException("Vous n'avez pas le droit d'acceder à cette ressource.");
        // // }
        // if ($this->getUser() === null) {
        //     return $this->redirectToRoute("security_login");
        // }
        // if (!$this->isGranted("ROLE_ADMIN")) {
        //     throw new AccessDeniedHttpException("Vous n'avez pas le droit d'acceder à cette ressource.");
        // }
        // $this->denyAccessUnlessGranted("ROLE_ADMIN", null, "Vous n'avez le droit d'accéder à cette ressouce");
        $category = $categoryRepository->find($id);
        // $this->denyAccessUnlessGranted("CAN_EDIT", $category);
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $category->setSlug(strtolower($slugger->slug($category->getName())));
            $em->flush();
            return $this->redirectToRoute('homepage');
        }
        $formView = $form->createView();
        return $this->render('category/edit.html.twig', [
            'category' => $category,
            'formView' => $formView
        ]);
    }

    public function renderMenuList()
    {
        $categories = $this->categoryRepository->findAll();
        return $this->render('category/_menu.html.twig', [
            'categories' => $categories
        ]);
    }
}
