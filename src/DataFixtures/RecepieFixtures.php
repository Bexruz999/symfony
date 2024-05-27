<?php

namespace App\DataFixtures;

use App\Entity\Category;
use App\Entity\Ingredient;
use App\Entity\Quantity;
use App\Entity\Recipe;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use FakerRestaurant\Provider\fr_FR\Restaurant;
use Symfony\Component\String\Slugger\SluggerInterface;

class RecepieFixtures extends Fixture implements DependentFixtureInterface
{

    public function __construct(private readonly SluggerInterface $slugger)
    {
    }

    public function load(ObjectManager $manager): void
    {

        $faker = Factory::create('fr_FR');
        $faker->addProvider(new Restaurant($faker));

        $ingredients = array_map(fn (string $name) => (new Ingredient())
            ->setName($name)
            ->setSlug(strtolower($this->slugger->slug($name)))
            , [
            'Farine',
            'Sucre',
            'Deufs',
            'Lait',
            'Sel',
            'Chocolate noir',
            'Vanille',
            'Banane'
        ]);

        $units = [
            'g', 'kg', 'l', 'ml', 'dl', 'cl', 'verre',
        ];

        foreach ($ingredients as $ingredient) {
            $manager->persist($ingredient);
        }

        $categories = ['Category 1', 'Category 2', 'Category 3', 'Category 4', 'Category 5', 'Category 6'];

        foreach ($categories as $category) {
            $cat = (new Category())
                ->setName($category)
                ->setSlug($this->slugger->slug($category))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()));
            $manager->persist($cat);
            $this->addReference($category, $cat);
        }


        for ($i = 0; $i < 10; $i++) {
            $title = $faker->foodName();
            $recipe = (new Recipe())
                ->setTitle($title)
                ->setSlug($this->slugger->slug($title))
                ->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTime()))
                ->setContent($faker->paragraph(10))
                ->setCategory($this->getReference($faker->randomElement($categories)))
                ->setDuration($faker->numberBetween(2, 60))
                ->setUser($this->getReference('user'.$faker->randomElement([1, 9])));;
                foreach ($faker->randomElements($ingredients, $faker->numberBetween(2, 5)) as $ingredient) {
                    $recipe->addQuantity((new Quantity())
                        ->setQuantity($faker->numberBetween(1, 250))
                        ->setUnit($faker->randomElement($units))
                        ->setIngredient($ingredient)
                    );
                }
            $manager->persist($recipe);
        }

        // $product = new Product();
        // $manager->persist($product);

        $manager->flush();
    }

    public function getDependencies()
    {
        return [UserFixtures::class];// TODO: Implement getDependencies() method.
    }
}
