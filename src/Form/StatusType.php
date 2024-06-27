<?php
/**
 * EPI License.
 */

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Validator\Constraints\Range;

/**
 * Form to assign users to bugs on the main page.
 */
class StatusType extends AbstractType
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
        // see StatusEnum.php
        $builder
            ->add('value', IntegerType::class, [
                'attr' => [
                    'hidden' => 'true',
                    'min' => 0,
                    'max' => 2,
                ],
                'constraints' => [
                    new Range(['min' => 0, 'max' => 2]),
                ],
            ]);
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
