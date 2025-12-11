<?php

namespace BestWishes\Form\Type;

use BestWishes\Entity\Gift;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GiftType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, ['trim' => true])
            ->add('image', ImageType::class, ['required' => false])
            ->add('moreDetailUrl', UrlType::class, ['required' => false])
            ->add('moreDetail', TextareaType::class, ['required' => false, 'trim' => true])
            ->add('save', SubmitType::class, ['label_format' => 'form.save']);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Gift::class,
            'label_format' => 'form.gift.%name%',
        ]);
    }
}
