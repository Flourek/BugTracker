<?php
/**
 * EPI License.
 */

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;

/**
 * Form for registration.
 */
class RegistrationFormType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder builder
     * @param array                $options options
     *
     * @return void void
     *              builds the form for registration
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add(
                'username',
                null,
                [
                    'attr' => ['class' => 'form-control'],
                ]
            )->add(
                'plainPassword',
                RepeatedType::class,
                [
                    'mapped' => false,
                    'attr' => ['autocomplete' => 'new-password', 'class' => 'form-control'],
                    'translation_domain' => 'validators',
                    'constraints' => [
                        new NotBlank(
                            [
                                'message' => 'Please enter a password',
                            ]
                        ),
                        new Length(
                            [
                                'min' => 6,
                                'minMessage' => 'Password at least {{ limit }} characters long',
                                // max length allowed by Symfony for security reasons
                                'max' => 4096,
                            ]
                        ),
                    ],
                    'type' => PasswordType::class,
                    'invalid_message' => 'The password fields must match',
                    'options' => ['attr' => ['class' => 'password-field']],
                    'required' => true,
                    'first_options' => ['label' => 'Password'],
                    'second_options' => ['label' => 'Repeat Password'],
                ]
            );
    }

    /**
     * @param OptionsResolver $resolver resolver
     *
     * @return void
     *              configures the options for the forms
     */
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(
            [
                'data_class' => User::class,
                'allow_extra_fields' => true,
            ]
        );
    }
}
