<?php

namespace mh\BTBundle\Controller\Frontend;

use \mh\BTBundle\Entity\Question;
use \mh\BTBundle\Entity\Answer;
use Pagerfanta\Pagerfanta;
use Pagerfanta\Adapter\DoctrineORMAdapter;
use Pagerfanta\Exception\NotValidCurrentPageException;

class QuestionController extends Base\BaseUserController
{
	public function listByCategoryAction($category_id)
	{
		$category = $this->getRepository('Category')->find($category_id);

		if (!$category) {
			throw $this->createNotFoundException('Нет категории.');
		}

		$query = $this->getRepository('Question')->createQueryBuilder('q')
			->select('q')
			->where('q.category = :category AND q.isPublished = true')
			->setParameter(':category', $category_id)
		;

		$questions = $this->getPaginated($query);

		return $this->render('Question:list_by_category.html.twig', array(
            'questions' => $questions,
			'category' => $category,
        ));
	}

	public function listAction()
	{
		$query = $this->getRepository('Question')->createQueryBuilder('q')
			->select('q')
			->where('q.isPublished = true')
			->orderBy('q.createdDate', 'DESC');
		;

		$questions = $this->getPaginated($query);

		return $this->render('Question:list.html.twig', array(
            'questions' => $questions,
        ));
	}

	public function showAction($id)
    {
		$question = $this->getQuestionOrException($id);

		$query = $this->getRepository('Answer')->createQueryBuilder('a')
			->select('a')
			->where('a.question = :question AND a.isPublished = true')
			->setParameter(':question', $question)
			->orderBy('a.createdDate', 'DESC');
		;

		$answers = $this->getPaginated($query);

		return $this->render('Question:show.html.twig', array(
            'question' => $question,
			'answers' => $answers,
		));
    }

	/*public function showAnswerAction($qid, $aid) {

		$answer = $this->getRepository('Answer')->findOneBy(array(
			'id' => $aid,
			'question' => $qid,
			'isPublished' => true,
		));

		$form = $this->createForm(new \mh\BTBundle\Form\Frontend\ObjectType());
		$form->setData(array('object_id' => $aid));

		if (!$answer) {
			throw $this->createNotFoundException('Ответа нет.');
		}

		return $this->render('Question:show_answer.html.twig', array(
			'answer' => $answer,
			'form' => $form->createView(),
		));
	}*/

	private function getQuestionOrException($id)
	{
		$question = $this->getRepository('Question')->findOneBy(array('id' => $id, 'isPublished' => true));
		if (!$question) {
			throw $this->createNotFoundException('Вопроса нет.');
		}

		return $question;
	}
}
