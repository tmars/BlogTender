<?php

namespace mh\BTBundle\Form\Frontend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;

class CommentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('content', 'textarea', array(
                'label' => 'Комментарий',
                'required' => true,
            ))
            ->add('post_id', 'hidden', array(
                'required' => true
            ))
        ;
    }

    public function getName()
    {
        return 'comment';
    }

     public function getDefaultOptions(array $options)
    {
        $collectionConstraint = new Collection(array(
            'allowExtraFields' => true,
            'fields' => array(
                'content' => array(
                    new NotBlank(array('message' => 'необходимо заполнить')),
                ),
                'post_id' => array(
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
