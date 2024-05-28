<?php

namespace App\Controller\Admin;

use App\Entity\UserCollection;
use App\Form\UserCollectionType;
use App\Repository\UserCollectionRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

#[Route('/admin/collection', name: 'admin.collection.')]
class UserCollectionController extends AbstractController
{
    #[Route('/', name: 'index')]
    public function index(UserCollectionRepository $repository, Request $request): Response
    {

        $page = $request->query->getInt('page', 1);
        $collections = $repository->paginateCollections($page);

        return $this->render('collection/index.html.twig', [
            'collections' => $collections,
            'items' => $repository->findAllWithCount()
        ]);
    }

    #[Route('/{slug}/{id}', name: 'show', methods: ['GET'], requirements: ['id' => Requirement::DIGITS])]
    public function show(Request $request, string $slug, int $id, UserCollectionRepository $repository): Response
    {

        $collection = $repository->find($id);
        if ($collection->getSlug() !== $slug) {
            return $this->redirectToRoute('admin.collection.show', ['slug' => $collection->getSlug(), 'id' => $recipe->getId()]);
        }

        return $this->render('collection/show.html.twig', [
            'collection' => $collection,
        ]);
    }

    #[Route('/{id}/edit', name: 'edit', methods: ['GET', 'POST'], requirements: ['id' => Requirement::DIGITS])]
    public function edit(Request $request, UserCollection $collection, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(UserCollectionType::class, $collection);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            /*$file = $form->get('thumbnailFile')->getData();
            $fileName = $recipe->getId() . '.' . $file->getClientOriginalExtension();
            $file->move($this->getParameter('kernel.project_dir') . '/public/reccetes/images', $fileName);
            $recipe->setThumbnail($fileName);*/
            $em->flush();
            $this->addFlash('success', 'Collection updated.');
            return $this->redirectToRoute('admin.collection.index');
        }

        return $this->render('collection/edit.html.twig', [
            'collection' => $collection,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/create', name: 'create', methods: ['GET', 'POST'])]
    public function create(Request $request, EntityManagerInterface $entityManager): Response
    {
        $collection = new UserCollection();
        $form = $this->createForm(UserCollectionType::class, $collection);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($collection);
            $entityManager->flush();
            return $this->redirectToRoute('admin.collection.index');
        }

        return $this->render('collection/create.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    #[Route('/{id}/delete', name: 'delete', methods: ['DELETE'], requirements: ['id' => Requirement::DIGITS])]
    public function remove(UserCollection $collection, EntityManagerInterface $em): Response {
        $em->remove($collection);
        $em->flush();
        $this->addFlash('success', 'Recipe deleted.');
        return $this->redirectToRoute('admin.collection.index');
    }
}
