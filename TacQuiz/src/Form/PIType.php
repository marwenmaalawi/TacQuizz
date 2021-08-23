<?php

namespace App\Form;

use App\Entity\PersonalInformations;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PIType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('information')
            ->add('type',ChoiceType::class, array(
                'choices' => array('Text' => "text",
                    'Image' => "image",
                    'Bool' => "bool",
                    'Date'=>"date")
            ))

        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => PersonalInformations::class,
        ]);
    }
}
