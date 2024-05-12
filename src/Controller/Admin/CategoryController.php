<?php

namespace App\Controller\Admin;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/category', name: 'admin.category.')]
#[IsGranted('ROLE_ADMIN')]
class CategoryController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(CategoryRepository $repository): Response
    {
        return $this->render('admin/category/index.html.twig', [
            'controller_name' => 'CategoryController',
            'categories' => $repository->findAll()
        ]);
    }

    #[Route('/{id}', name: 'show', requirements: ['id' => Requirement::DIGITS], methods: ['GET'])]
    public function show(Category $category): Response
    {
        return $this->render('admin/category/show.html.twig', ['category' => $category]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $em): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $em->persist($category);
            $em->flush();
            $this->addFlash('success', 'Category created.');
            return $this->redirectToRoute('admin.category.index');
        }

        return $this->render('admin/category/create.html.twig', [
            'form' => $form->createView(),
            'category' => $category,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', requirements: ['id' => Requirement::DIGITS], methods: ['GET', 'POST'])]
    public function edit(Category $category, EntityManagerInterface $em, Request $request): Response
    {
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();
            $this->addFlash('success', 'Category updated.');
            return $this->redirectToRoute('admin.category.index');
        }
        return $this->render('admin/category/edit.html.twig', [
            'category' => $category,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'delete', requirements: ['id' => Requirement::DIGITS], methods: ['DELETE'])]
    public function delete(Category $category, EntityManagerInterface $em): Response
    {
        $em->remove($category);
        $em->flush();
        $this->addFlash('success', 'Category deleted.');
        return $this->redirectToRoute('admin.category.index');
    }
}
