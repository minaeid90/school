<?php
namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;


class StudentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class, ['attr'=>['class'=>'form-control']])
            ->add('classroom', EntityType::class, ['attr'=>['class'=>'form-control'],'required'=>true,'class'=>'AppBundle\Entity\Classroom','choice_label'=>'name'])
            ->add('age', TextType::class, ['attr'=>['class'=>'form-control']])
            ->add('image', FileType::class, ['label' => 'Image', 'attr'=>['class'=>'form-control-file'],'data_class' => null, 'required'=>false])
            ->add('submit', SubmitType::class, ['label'=>'Save', 'attr'=>['class'=>'form-control  mt-2']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'AppBundle\Entity\Student',
        ]);
    }
}