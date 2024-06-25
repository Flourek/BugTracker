<?php
/**
 * EPI License.
 */

namespace App\Form;

use App\Entity\Comment;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Form to post a comment under a bug.
 */
class CommentType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder builder
     * @param array                $options options
     *
     * @return void void
     *              builds the form to post a comment
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('body')
            ->add(
                'save',
                SubmitType::class,
                ['attr' => ['class' => 'save']]
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
                'data_class' => Comment::class,
            ]
        );
    }
}
