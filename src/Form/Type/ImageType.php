<?php

namespace BestWishes\Form\Type;

use BestWishes\Entity\Image;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Translation\TranslatableMessage;

class ImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('url', UrlType::class, ['required' => false,
            'help' => new TranslatableMessage('form.gift.image.url.help'),]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Image::class,
            'label_format' => 'form.gift.image.%name%',
            'empty_data' => fn (FormInterface $form) => new Image($form->get('url')->getData()),
        ]);
    }
}
