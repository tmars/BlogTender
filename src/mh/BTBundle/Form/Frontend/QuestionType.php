<?php

namespace mh\BTBundle\Form\Frontend;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Collection;
use Symfony\Component\Validator\Constraints\NotBlank;

class QuestionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', array(
                'label' => 'Заголовок',
                'required' => true
            ))
            ->add('content', 'textarea', array(
                'label' => 'Вопрос',
                'required' => true
            ))
            ->add('category', 'entity', array(
                'label' => 'Категория',
                'class' => 'BTBundle:Category',
                'property' => 'label',
                'multiple' => false,
                'required' => true,
                'expanded' => true,
            ))
        ;
    }

    public function getName()
    {
        return 'question';
    }

     public function getDefaultOptions(array $options)
    {
        $collectionConstraint = new Collection(array(
            'allowExtraFields' => true,
            'fields' => array(
                'title' => array(
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
