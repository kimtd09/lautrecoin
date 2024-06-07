<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Department;
use App\Entity\Product;
use App\Entity\Region;
use Doctrine\DBAL\Types\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add("username")
            ->add('title')
            ->add('city')
            ->add('zipcode')
            ->add('price')
            ->add('publish_date', null, [
                'widget' => 'single_text',
            ])
            // ->add('publish_date', DateType::class, [
            //     'widget' => 'single_text',
                
            //     // prevents rendering it as type="date", to avoid HTML5 date pickers
            //     'html5' => false,
            //     ])
            ->add('url')
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'choice_label' => 'name',
            ])
            ->add('region', EntityType::class, [
                'class' => Region::class,
                'choice_label' => 'name',
            ])
            ->add('department', EntityType::class, [
                'class' => Department::class,
                'choice_label' => 'name',
            ])
            ->add('description')
            ->add('submit', SubmitType::class, ['label' => $options['button_label']]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
            'button_label' => 'Submit'
        ]);
    }
}
