<?php
namespace AppBundle\Form;

use App\Entity\ProductImage;
use AppBundle\Entity\Product;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;

class ProductType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name')

            ->add('product_images', CollectionType::class, [
                'entry_type' => ProductImageType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'prototype' => true
            ])
            ->add('imageFile',VichImageType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Product::class,
        ]);
    }
}
