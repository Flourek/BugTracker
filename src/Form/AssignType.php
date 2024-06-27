<?php
/**
 * EPI License.
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form to assign users to bugs on the main page.
 */
class AssignType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder builder
     * @param array                $options options
     *
     * @return void void
     *              builds the form for assigning users to bugs
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username', TextType::class, ['allow_extra_fields' => true])
            ->add('toDelete');
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
    }
}
