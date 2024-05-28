<?php

namespace App\Controller\Admin;

use App\Entity\Item;
use App\Form\ItemType;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/item', name: 'admin.item.')]
class ItemController extends AbstractController
{
    #[Route('/', name: 'index', methods: ['GET'])]
    public function index(Request $request, ItemRepository $repository): Response
    {
        $page = $request->query->getInt('page', 1);
        $items = $repository->paginateItems($page);

        return $this->render('item/index.html.twig', [
            'items' => $items,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, ItemRepository $repository): Response
    {
        $item = $repository->find($id);

        return $this->render('item/show.html.twig', [
            'item' => $item,
        ]);
    }
    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request,Item $item, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $entityManager->flush();
            $this->addFlash('success', 'Item updated.');
            return $this->redirectToRoute('admin.item.index');
        }

        return $this->render('item/edit.html.twig', [
            'item' => $item,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $entityManager->persist($item);
            $entityManager->flush();

            $this->addFlash('success', 'Item created successfully.');
            return $this->redirectToRoute('admin.item.index');
        }

        return $this->render('item/create.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(Item $item, EntityManagerInterface $em): Response {
        $em->remove($item);
        $em->flush();
        $this->addFlash('success', 'Item deleted.');
        return $this->redirectToRoute('admin.item.index');
    }

}
