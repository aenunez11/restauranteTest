<?php

namespace Form;

use Domain\Entity\Restaurant;
use Domain\Entity\Segment;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SegmentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class,[
                'attr' => array('class' => 'form-control','style' => 'width:50%;margin-bottom:20px','placeholder' =>' Ingrese el nombre del segmento'),
                'required' => true
            ])
            ->add('uidentifier', TextType::class, [
                'attr' => array('class' => 'form-control','style' => 'width:50%;margin-bottom:20px','placeholder' =>' Ingrese el identificador del segmento'),
                'required' => true
            ])
            ->add('restaurants',EntityType::class,[
                'class' => Restaurant::class,
                'choice_label' => 'name',
                'multiple' => true,
                'attr' => array('class' => 'form-control','style' => 'width:50%;margin-bottom:20px'),
                'required' => true
                ])
            ->add('save', SubmitType::class, [
                'label' => "Guardar",
                'attr' => array('class' => 'btn btn-primary')
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(
            [
                'data_class' => Segment::class,
            ]
        );
    }

}
