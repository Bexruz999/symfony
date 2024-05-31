<?php

namespace App\Controller\Admin;

use App\Entity\Item;
use App\Form\ItemType;
use App\Repository\ItemRepository;
use App\Repository\UserCollectionRepository;
use App\Security\Voter\ItemVoter;
use App\Security\Voter\UserCollectionVoter;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/admin/item', name: 'admin.item.')]
class ItemController extends AbstractController
{
    public function __construct(private readonly Security $security)
    {
    }

    #[Route('/', name: 'index', methods: ['GET'])]
    #[IsGranted(ItemVoter::LIST)]
    public function index(Request $request, ItemRepository $repository, Security $security): Response
    {

        $page = $request->query->getInt('page', 1);
        $userId = $security->getUser()->getId();
        $cantListAll = $security->isGranted(UserCollectionVoter::LIST_ALL);

        if ($tag = $request->query->get('tags')) {

            $items = $repository->paginateItemsWithTag($page, $tag,$cantListAll ? null : $userId);

        } else {
            $items = $repository->paginateItems($page,$cantListAll ? null : $userId);
        }

        return $this->render('item/index.html.twig', [
            'items' => $items,
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function show(int $id, ItemRepository $repository): Response
    {
        $item = $repository->find($id);

        return $this->render('item/show.html.twig', ['item' => $item,]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'])]
    public function edit(Request $request,Item $item, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(ItemType::class, $item);
        $form->handleRequest($request);
        $collection = $item->getUserCollection();
        if ($form->isSubmitted()) {

            $entityManager->persist($item);
            $entityManager->flush();
            $this->addFlash('success', 'Item updated.');
            return $this->redirectToRoute('admin.collection.show', ['id' => $collection->getId(), 'slug' => $collection->getSlug()]);

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

    #[Route('/{id}/add', name: 'add', methods: ['GET', 'POST'])]
    public function add($id, Request $request, EntityManagerInterface $entityManager, Security $security, UserCollectionRepository $collectionRepository): Response
    {
        $collection = $collectionRepository->find($id);
        $item = new Item();
        $form = $this->createForm(ItemType::class, $item);
        $form->remove('UserCollection');
        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $item->setUserCollection($collection);
            $item->setUser($security->getUser());
            $entityManager->persist($item);
            $entityManager->flush();

            $this->addFlash('success', 'Item created successfully.');
            return $this->redirectToRoute('admin.collection.show', ['id' => $id, 'slug' => $collection->getSlug()]);
        }

        return $this->render('item/add.html.twig', [
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
