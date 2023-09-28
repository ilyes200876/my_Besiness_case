<?php

namespace App\Form;

use App\Entity\Nft;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class NftType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('price')
            ->add('createdAt')
            ->add('title')
            ->add('src')
            ->add('weight')
            ->add('format')
            ->add('description')
            ->add('user')
            ->add('subCategories')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Nft::class,
        ]);
    }
}
