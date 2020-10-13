<?php
/*
 * Copyright (c) 2020. | All Rights Reserved
 * Ce code source est la propriété de <faso-dev> http://faso-dev.com
 * Ce code source ne saurait être reproduit sans une autorisation expresse de <faso-dev>
 * @Author <faso-dev> jeromeonadja28@gmail.com
 */

namespace App\Form;

use App\Entity\Category;
use App\Entity\Tutorial;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Class TutorialType
 * @package App\Form
 */
class TutorialType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, [
                'label' => "Titre du tutorial ",
                'required' => true
            ])
            ->add('description', TextareaType::class, [
                'label' => "Description du tutoriel ",
                'required' => true,
                'attr' => [
                    'rows' => 5
                ]
            ])
            ->add('url', UrlType::class, [
                'label' => 'Url de la vidéo ',
                'required' => true
            ])
            ->add('category', EntityType::class, [
                'label' => 'Catégorie du tutoriel ',
                'required' => true,
                'class' => Category::class,
                'choice_label' => 'title'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Tutorial::class,
        ]);
    }
}
