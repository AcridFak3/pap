<?php

// src/Form/TicketType.php

namespace App\Form\ticket;

use App\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Title',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'Description',
            ])
            ->add('status', ChoiceType::class, [
                'choices' => [
                    'Pending' => 'P',
                    'Working' => 'W',
                    'Finished' => 'F',
                ],
                'label' => 'Status',
                'disabled' => true,
            ])
            ->add('priority', ChoiceType::class, [

                'choices' => [
                    'Low' => 'L',
                    'Medium' => 'M',
                    'High' => 'H',
                ],
                'label' => 'Priority',
                'disabled' => true,
            ])
            ->add('createdAt', DateTimeType::class, options: [
                'label' => 'Created At',
                'disabled' => true,  // Não é editável, o valor será gerado automaticamente no controlador
            ])
             ->add('updatedAt', DateTimeType::class, [
                 'label' => 'Updated At',
                 'disabled' => true,  // Não é editável, o valor será gerado automaticamente no controlador
             ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
