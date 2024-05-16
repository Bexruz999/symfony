<?php

namespace App\Controller\API;

use App\DTO\PaginatorDTO;
use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Attribute\MapQueryString;
use Symfony\Component\HttpKernel\Attribute\MapRequestPayload;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;
use Symfony\Component\Serializer\Normalizer\AbstractNormalizer;
use Symfony\Component\Serializer\SerializerInterface;

class RecipesController extends AbstractController
{
    #[Route('/api/recipes', methods: ['GET'])]
    public function index(
        RecipeRepository $repository,
        #[MapQueryString]
        ?PaginatorDTO $paginatorDTO = null
    )
    {
        $recipes = $repository->paginateRecipes($paginatorDTO?->page ?? 1);

        return  $this->json($recipes, Response::HTTP_OK, [], ['groups' => ['recipes.index']]);
    }

    #[Route('/api/recipes/{id}', methods: ['GET'], requirements: ['id'=> Requirement::DIGITS])]
    public function show(Recipe $recipe): Response
    {
        return  $this->json($recipe, Response::HTTP_OK, [], ['groups' => ['recipes.index', 'recipes.show']]);
    }

    #[Route('/api/recipes', methods: ['POST'])]
    public function create(
        Request $request,
        #[MapRequestPayload(
            serializationContext: [
                'groups' => ['recipes.create'],
            ]
        )]
        Recipe $recipe,
        EntityManagerInterface $em,
    )
    {
        $recipe->setCreatedAt(new \DateTimeImmutable);
        $recipe->setUpdatedAt(new \DateTimeImmutable);
        //dd($recipe);
        $recipe->setCategoryId(6);
        $recipe->setCategoryId(6);
        $recipe->setCategoryId(6);
        $em->persist($recipe);
        $em->flush();
        return $this->json($recipe, 200, [], ['groups' => ['recipes.show', 'recipes.index']]);
    }
}
