<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Dish;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\MoneyType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class DishType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'constraints' => [
                    new NotBlank([
                        'message' => 'Cannot be blank',
                    ]),
                ],
            ])
            ->add('image')
            ->add('description')
            ->add('price', MoneyType::class, [
                'currency'=>'EUR',
                'label' => 'Prix',
//                'attr'=> ['disabled'=>true,'class'=>'to-normalize totalHT text-end  no-input']
                'constraints' => [
                    new NotBlank([
                        'message' => 'Cannot be blank',
                    ]),
                ],
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'constraints' => [
                    new NotBlank([
                        'message' => 'Cannot be blank',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Dish::class,
        ]);
    }
}
