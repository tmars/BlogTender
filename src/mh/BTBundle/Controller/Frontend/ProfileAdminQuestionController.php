<?php

namespace mh\BTBundle\Controller\Frontend;

class ProfileAdminQuestionController extends Base\BaseUserController
{

	public function listAction()
    {
        $user = $this->getUserOrException();

		$query = $this->getRepository('Question')-> createQueryBuilder('q')
			->select('q')
            ->where('q.user = :user')
            ->setParameter(':user', $user)
		;

		$questions = $this->getPaginated($query);

		return $this->render("ProfileAdminQuestion:list.html.twig", array(
			'questions' => $questions,
		));
    }

	public function newAction()
    {
		$user = $this->getActiveUserOrException();
		$form = $this->createForm(new \mh\BTBundle\Form\Frontend\QuestionType());
        $request = $this->getRequest();

        while(1) {

            if ($request->getMethod() != 'POST') {
			   break;
            }

            $form->bindRequest($request);

            if ( ! $form->isValid()) {
                break;
            }

			$data = $form->getData();

			$question = new \mh\BTBundle\Entity\Question();
			$question->setUser($user);
			$question->setTitle($data['title']);
			$question->setContent($data['content']);
			$question->setCategory($data['category']);

			$allocator = $this->get('scores_allocator');
			$allocator->allocateScoresForQuestion($question);

			$em = $this->getEM();
			$em->persist($question);
			$em->flush();

			return $this->redirect($this->generateUrl('list_question'));
		}

        return $this->render('Question:new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

	public function deleteAction($id)
	{
		$user = $this->getActiveUserOrException();
		$question = $this->getQuestionOrException($id);

		if (!$question) {
			throw $this->createNotFoundException('Доступа нет.');
		}

		$em = $this->getEM();
		$em->remove($question);
		$em->flush();

		return $this->redirect($this->generateUrl('profile_admin_list_question'));
	}

	public function answerAction($id) {
		$user = $this->getActiveUserOrException();
		$question = $this->getQuestionOrException($id);

		if ($user->getId() == $question->getUser()->getId()) {
			throw $this->createNotFoundException('Вы не можете ответить на свой вопрос.');
		}

		$form = $this->createForm(new \mh\BTBundle\Form\Frontend\AnswerType());
        $request = $this->getRequest();

		while(1) {

            if ($request->getMethod() != 'POST') {
			   break;
            }

            $form->bindRequest($request);

            if ( ! $form->isValid()) {
                break;
            }

			$data = $form->getData();

			$answer = new \mh\BTBundle\Entity\Answer();
			$answer->setUser($user);
			$answer->setContent($data['content']);
			$answer->setQuestion($question);

			$allocator = $this->get('scores_allocator');
			$allocator->allocateScoresForAnswer($answer);

			$question->setAnswerCount($question->getAnswerCount() + 1);
			$em = $this->getEM();
			$em->persist($answer);
			$em->flush();

			return $this->redirect($this->generateUrl('show_question', array(
				'id' => $question->getId(),
			)));
		}

		return $this->render('Question:new_answer.html.twig', array(
            'question' => $question,
			'form' => $form->createView(),
		));
	}

	private function getQuestionOrException($id)
	{
		$question = $this->getRepository('Question')->findOneBy(array('id' => $id, 'isPublished' => true));
		if (!$question) {
			throw $this->createNotFoundException('Вопроса нет.');
		}

		return $question;
	}
}
