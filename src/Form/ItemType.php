<?php

namespace App\Form;

use App\Entity\Item;
use App\Entity\User;
use App\Entity\UserCollection;
use App\Repository\UserCollectionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ItemType extends AbstractType
{

    public function __construct(private FormListenerFactory $factory, private Security $security)
    {
    }
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'Name',
            ])
            ->add('content', TextareaType::class, [
                'label' => 'Content',
            ])
            /*->add('created_at', null, [
                'widget' => 'single_text',
            ])
            ->add('updated_at', null, [
                'widget' => 'single_text',
            ])*/
            /*->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
            ])*/
            ->add('UserCollection', EntityType::class, [
                'label' => 'Collection',
                'class' => UserCollection::class,
                'choice_label' => 'name',
                'query_builder' => function (UserCollectionRepository $repository) use($options) {
                    return $repository->createQueryBuilder('uc')
                        ->where('uc.user = :user')
                        ->setParameter(':user', $options['data']->getUser() ?? $this->security->getUser());
                }
            ])
            ->add('save', SubmitType::class, [
                'label' => 'Save',
            ])
            ->addEventListener(FormEvents::POST_SUBMIT, $this->factory->setUser())
            ->addEventListener(FormEvents::POST_SUBMIT, $this->factory->timestamps());
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Item::class,
        ]);
    }
}
