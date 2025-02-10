<?php

namespace App\Form;

use App\Entity\TicketLog;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketLogType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('action', TextareaType::class, [
                'label' => 'Descrição da Ação',
                'attr' => [
                    'class' => 'w-full p-2 border border-gray-300 rounded-lg',
                    'placeholder' => 'Descreva a ação realizada no ticket',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TicketLog::class,
        ]);
    }
}
