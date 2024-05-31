<?php

namespace App\Form;

use App\Entity\Item;
use App\Entity\Tag;
use App\Repository\TagRepository;
use Symfony\Bridge\Doctrine\Form\DataTransformer\CollectionToArrayTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{

    public function __construct(public TagRepository $repository)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        //$builder->add('name', TextType::class);

        $builder->addModelTransformer(new CollectionToArrayTransformer(), true);

        $builder->addModelTransformer(new CallbackTransformer(
            function ($tagsAsArray): string {
                // transform the array to a string
                if (is_array($tagsAsArray)) {
                    return implode(', ', $tagsAsArray);
                }
                return '';
            },
            function ($tagsAsString): array {
                // transform the string back to an array
                $names = array_unique(array_filter(array_map('trim', explode(', ', $tagsAsString))));
                $tags = $this->repository->findBy(['name' => $names]);
                $newNames = array_diff($names, $tags);

                foreach ($newNames as $name) {
                    $tag = new Tag();
                    $tag->setName($name);
                    $tags[] = $tag;
                }
                return $tags;
            }
        ), true);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
            'required' => false,
            'attr' => [
                'class' => 'tag-input',
                'data-allow-new' => true
            ]
        ]);
    }

    public function getParent()
    {
        return TextType::class;
    }
}
