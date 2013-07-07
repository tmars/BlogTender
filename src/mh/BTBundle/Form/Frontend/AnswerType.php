<?php

namespace mh\BTBundle\Form\Frontend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;

class AnswerType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', 'textarea', array(
                'label' => 'Контент',
                'required' => true,
                'attr' => array(
                    'class' => 'tinymce',
                    'data-theme' => 'advanced',
                )
            ))
        ;
    }

    public function getName()
    {
        return 'answer';
    }

     public function getDefaultOptions(array $options)
    {
        $collectionConstraint = new Collection(array(
            'allowExtraFields' => true,
            'fields' => array(
                'content' => array(
                    new NotBlank(array('message' => 'необходимо заполнить')),
                ),
            ),
        ));

        return array(
            'validation_constraint' => $collectionConstraint,
            'csrf_protection' => true,
            'intention'       => $this->getName(),
        );
    }
}
