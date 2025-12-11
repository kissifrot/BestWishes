<?php

namespace BestWishes\Form\Type;

use BestWishes\Entity\GiftList;
use BestWishes\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GiftListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['trim' => true])
            ->add('owner', EntityType::class, ['class' => User::class, 'choice_label' => 'name'])
            ->add('birthDate', DateType::class, ['years' => range(date('Y') - 120, date('Y'))])
            ->add('save', SubmitType::class, ['label_format' => 'form.save']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => GiftList::class,
            'label_format' => 'form.gift_list.%name%',
        ]);
    }
}
