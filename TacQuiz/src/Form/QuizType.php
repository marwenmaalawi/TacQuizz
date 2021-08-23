<?php

namespace App\Form;

use App\Entity\Category;
use App\Entity\Quiz;
use App\Repository\CategoryRepository;
use phpDocumentor\Reflection\Types\Integer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Validator\Constraints\DateTime;

class QuizType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {$id=$options['data']->getUser()->getId();

        $builder
            ->add('Category',EntityType::class,[
                 'class' => Category::class,
                'query_builder' => function (CategoryRepository $er) use ($id) {
                    return $er->createQueryBuilder('u')

                        ->where('u.User=:us')
                        ->setParameter('us',$id) ;

                }
            ])

            ->add('Title')
            ->add('date_quiz', DateType::class)
            ->add('image', FileType::class, [
                'mapped' => false,
                'required' => false,
            ])
            ->add('publicResult',CheckboxType::class, [
                'label_attr' => ['class' => 'switch-custom'],'required' => false,
            ])
            ->add('random',CheckboxType::class, [
        'label_attr' => ['class' => 'switch-custom'],'required' => false,

     ])
            ->add('timer',CheckboxType::class, [
                'label_attr' => ['class' => 'switch-custom'],'required' => false,


            ])
            ->add('Quiztime',TextareaType::class,[
                'attr' => array('style' => 'height: 35','width: 50'),
                'mapped' => false,
                'required' => false,])


            ->add('minScore',ChoiceType::class, array(
                'choices' => array('50%' => 50,'10%' => 10,'20%' => 20,'30%' => 30,'40%' => 40,
                    '60%' => 60,'70%' => 70,'80%' => 80,
                    '90%' => 90,'100%' => 100,)));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Quiz::class,
        ]);
    }
}
