<?php

namespace App\Form;

use App\Entity\Question;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('answerType',ChoiceType::class, array(
                'choices' => array('Text' => "text",
                    'Image' => "image",)
            ))

            ->add('imageLink', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])

            ->add('question',TextareaType::class,array(

                'attr' => array('style' => 'height: 300','width: 300')
            ))


        ;
    }
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Question::class,
        ]);
    }
}
