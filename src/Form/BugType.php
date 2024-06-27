<?php
/**
 * EPI License.
 */

namespace App\Form;

use App\Entity\Bug;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form to create a new bug on the report page.
 */
class BugType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder builder
     * @param array                $options options
     *
     * @return void void
     *              builds the form for reporting a new bug
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', null, ['attr' => ['autocomplete' => 'new-password', 'class' => 'form-control']])
            ->add('environment', null, ['attr' => ['class' => 'form-control']])
            ->add('version', null, ['attr' => ['class' => 'form-control']])
            ->add('body', null, ['attr' => ['class' => 'form-control']])
            ->add(
                'imageFiles',
                FileType::class,
                [
                    'multiple' => true,
                    'required' => false,
                    'mapped' => false,
                    'attr' => [
                        'accept' => 'image/*',
                        'multiple' => 'multiple',
                        'class' => 'form-control',
                    ],
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver resolver
     *
     * @return void void
     *
     * the options of the form
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => Bug::class,
            ]
        );
    }
}
