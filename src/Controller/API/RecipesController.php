<?php

namespace App\Controller\API;

use App\Entity\Recipe;
use App\Repository\RecipeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Requirement\Requirement;

class RecipesController extends AbstractController
{
    #[Route('/api/recipes')]
    public function index(RecipeRepository $repository)
    {
        $recipes = $repository->findAll();

        return  $this->json($recipes, Response::HTTP_OK, [], ['groups' => ['recipes.index']]);
    }

    #[Route('/api/recipes/{id}', name: 'recipes.show', methods: ['GET'], requirements: ['id'=> Requirement::DIGITS])]
    public function show(Recipe $recipe): Response
    {
        return  $this->json($recipe, Response::HTTP_OK, [], ['groups' => ['recipes.index', 'recipes.show']]);
    }
}
