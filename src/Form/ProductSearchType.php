<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Product;
use App\Entity\Vendor;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProductSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price_range', ChoiceType::class, [
                'choices' => [
                    '1-99' => '1-99',
                    '100-499' => '100-499',
                    '500-999' => '500-999',
                    '1000-10000' => '1000-10000'
                ],
                'expanded' => true,
                'required' => false
            ])
            ->add('category', EntityType::class, [
                'class' => Category::class,
                'expanded' => true,
                'required' => false
            ])
            ->add('vendor', EntityType::class, [
                'class' => Vendor::class,
                'expanded' => true,
                'required' => false
            ])
            ->add('Filter', SubmitType::class);
    }
}
