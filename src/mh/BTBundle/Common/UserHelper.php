<?php
/**
 * Created by JetBrains PhpStorm.
 * User: marcky
 * Date: 20.10.13
 * Time: 14:17
 * To change this template use File | Settings | File Templates.
 */

namespace mh\BTBundle\Common;

use mh\Common\Random;
use mh\BTBundle\Entity as Entity;

class UserHelper {

    private $em;
    private $cookie;
    private $request;

    public function __construct($em, $cookie, $request)
    {
        $this->em = $em;
        $this->cookie = $cookie;
        $this->request = $request;
    }

    public function getUniqueLoginByEmail($email)
    {
        $login = substr($email, 0, strpos($email, '@'));
        $login = $this->getUniqueLogin($login);
        return $login;
    }

    public function getUniqueLogin($login)
    {
        if (strlen($login) > 28) {
            $login = substr($login, 28);
        } else if (strlen($login) < 6) {
            $login = 'login_';
        }

        $postfix = '';
        $login = $this->getFormatedLogin($login);
        while ($this->em->getRepository("BTBundle:User")->findOneByLogin($login.$postfix)) {
            $postfix = $postfix ? $postfix + 1 : 2;
        }
        return $login.$postfix;
    }

    public function getFormatedLogin($login)
    {
        $login = preg_replace('/\W/', '_', $login);
        $login = preg_replace('/_+/', '_', $login);
        $login = preg_replace('/^_+/', '', $login);
        $login = preg_replace('/_+$/', '', $login);

        return $login;
    }

    public function getUserEmailConfirmCode($user)
    {
        $beforeCode = $this->em->getRepository("BTBundle:UserEmailConfirm")->findOneByUser($user);
        if ($beforeCode) {
            $this->em->remove($beforeCode);
            $this->em->flush();
        }

        $code = Random::generate(array('length' => 32));

        $confirm = new Entity\UserEmailConfirm();
        $confirm->setUser($user);
        $confirm->setCode($code);

        $this->em->persist($confirm);

        return $confirm->getCode();
    }

    public function createNewUserSession(Entity\User &$user)
    {
        // Генерируем хэш авторизации
        $hash = Random::generate(array('length' => 32));

        // Создаём сессию
        $session = new Entity\UserSession();
        $session->setHash($hash);
        $session->setIp($this->request->getClientIp());
        $session->setUser($user);
        $session->setUserAgent($_SERVER['HTTP_USER_AGENT']);
        $this->em->persist($session);

        // Сохраняем в бд
        $user->setCurrentSession($session);
        $this->em->flush();

        // Сохраняем хеш в печеньках
        $this->cookie->set('auth_hash', $session->getHash());
    }

    public function setInvitingUserIfExist(Entity\User &$user)
    {
        if (($inviteCode = $this->cookie->get('invite')) &&
            ($invitingUser = $this->em->getRepository('BTBundle:User')->findOneByInviteCode($inviteCode))) {
            $user->setInvitingUser($invitingUser);
        }
    }

}