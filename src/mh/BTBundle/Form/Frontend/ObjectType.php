<?php

namespace mh\BTBundle\Form\Frontend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;

class ObjectType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('object_id', 'hidden', array('required' => true))
        ;
    }

    public function getName()
    {
        return 'object';
    }

     public function getDefaultOptions(array $options)
    {
        $collectionConstraint = new Collection(array(
            'allowExtraFields' => true,
            'fields' => array(
                'object_id' => array(
                    new NotBlank(array('message' => 'необходимо заполнить234')),
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
