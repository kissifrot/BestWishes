<?php

namespace AppBundle\Form\Type;

use AppBundle\Entity\GiftList;
use AppBundle\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GiftListType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class)
            ->add('owner', EntityType::class, ['class' => User::class, 'choice_label' => 'name'])
            ->add('birthDate', DateType::class, ['years' => range(date('Y') - 120, date('Y'))])
            ->add('save', SubmitType::class, ['label_format' => 'form.save']);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => GiftList::class,
            'label_format' => 'form.gift_list.%name%'
        ));
    }
}
