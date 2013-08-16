<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * User
 */
class User
{
	const SOURCE_INTERNAL = 0;
    const SOURCE_FROM_VK = 1;
    const SOURCE_FROM_FB = 2;
    const SOURCE_FROM_ML = 3;
    const SOURCE_FROM_GL = 4;
    const SOURCE_FROM_TW = 5;
    const SOURCE_FROM_OD = 6;

    public static function getSourceByMode($mode)
    {
        switch ($mode) {
            case 'vk': return self::SOURCE_FROM_VK;
            case 'fb': return self::SOURCE_FROM_FB;
            case 'ml': return self::SOURCE_FROM_ML;
            case 'gl': return self::SOURCE_FROM_GL;
            case 'tw': return self::SOURCE_FROM_TW;
            case 'od': return self::SOURCE_FROM_OD;
        }
    }

	public function getSourceText()
	{
		switch ($this->getSource()) {
            case self::SOURCE_INTERNAL: return 'внутренн€€ система';
            case self::SOURCE_FROM_VK: return 'vk.com';
            case self::SOURCE_FROM_FB: return 'facebook.com';
            case self::SOURCE_FROM_ML: return 'mail.ru';
            case self::SOURCE_FROM_GL: return 'plus.google.com';
            case self::SOURCE_FROM_TW: return 'twitter.com';
            case self::SOURCE_FROM_OD: return 'odnoklassniki.ru';
        }
	}

    public function __toString()
    {
        return $this->getScreenName();
    }

    /**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
        $this->setCreatedDate(new \DateTime());
        $this->setCountersDay(new \DateTime());
        $this->setScores(0);
        $this->setCountAnswerAtDay(0);
        $this->setCountQuestionAtDay(0);
        $this->setCountPostForeignLinkAtDay(0);
        $this->setCountPostForeignLinkOnTrustedSiteAtDay(0);
		if (!$this->getSalt()) {
			$salt = \mh\Common\Random::generate(array('length' => 10));
			$this->setSalt($salt);
			$this->setPassword($this->passwordEncode($this->getPassword()));
		}
		if (!$this->getInviteCode()) {
			$code = \mh\Common\Random::generate(array('length' => 10));
			$this->setInviteCode($code);
		}
    }


    public function passwordEncode($password)
    {
        return md5($password . 'sd' . $this->getSalt());
    }

    public function resetCounters()
    {
        $datel = mktime(0, 0, 0, date("m"), date("d"), date("Y"));
		$d = $this->getCountersDay();
		$date2 = mktime(0, 0, 0, $d->format('m'), $d->format('d'), $d->format('Y'));
		if ($datel != $date2) {
            $this->setCountersDay(new \DateTime());
            $this->setCountPostAtDay(0);
            $this->setCountAnswerAtDay(0);
            $this->setCountQuestionAtDay(0);
            $this->setCountPostForeignLinkAtDay(0);
            $this->setCountPostForeignLinkOnTrustedSiteAtDay(0);
        }
    }

	public function getAvatar()
    {
		$image = $this->getFoto() ? $this->getFoto() : new UserFoto();
		return $image->getBrowserPath();
    }

	public function setScreenName($screenName)
    {
        $this->screenName = strtolower($screenName);

        return $this;
    }
    /*--------------------------------------------------------------------------*/

    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $salt;

    /**
     * @var string
     */
    private $password;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $about;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string
     */
    private $screenName;

    /**
     * @var boolean
     */
    private $emailConfirmed;

    /**
     * @var \DateTime
     */
    private $createdDate;

    /**
     * @var integer
     */
    private $source;

    /**
     * @var string
     */
    private $idInSource;

    /**
     * @var string
     */
    private $socnetInfo;

    /**
     * @var integer
     */
    private $scores;

    /**
     * @var \DateTime
     */
    private $countersDay;

    /**
     * @var integer
     */
    private $countQuestionAtDay;

    /**
     * @var integer
     */
    private $countAnswerAtDay;

    /**
     * @var integer
     */
    private $countPostForeignLinkAtDay;

    /**
     * @var integer
     */
    private $countPostForeignLinkOnTrustedSiteAtDay;

    /**
     * @var \mh\BTBundle\Entity\UserFoto
     */
    private $foto;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $sessions;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $posts;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $answers;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $questions;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $postComments;

    /**
     * @var \mh\BTBundle\Entity\UserSession
     */
    private $currentSession;

    /**
     * Constructor
     */


    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set salt
     *
     * @param string $salt
     * @return User
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get salt
     *
     * @return string
     */
    public function getSalt()
    {
        return $this->salt;
    }

    /**
     * Get password
     *
     * @return string
     */
    public function getPassword()
    {
        return $this->password;
    }

    /**
     * Set name
     *
     * @param string $name
     * @return User
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set about
     *
     * @param string $about
     * @return User
     */
    public function setAbout($about)
    {
        $this->about = $about;

        return $this;
    }

    /**
     * Get about
     *
     * @return string
     */
    public function getAbout()
    {
        return $this->about;
    }

    /**
     * Set email
     *
     * @param string $email
     * @return User
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Get screenName
     *
     * @return string
     */
    public function getScreenName()
    {
        return $this->screenName;
    }

    /**
     * Set emailConfirmed
     *
     * @param boolean $emailConfirmed
     * @return User
     */
    public function setEmailConfirmed($emailConfirmed)
    {
        $this->emailConfirmed = $emailConfirmed;

        return $this;
    }

    /**
     * Get emailConfirmed
     *
     * @return boolean
     */
    public function getEmailConfirmed()
    {
        return $this->emailConfirmed;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return User
     */
    public function setCreatedDate($createdDate)
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    /**
     * Get createdDate
     *
     * @return \DateTime
     */
    public function getCreatedDate()
    {
        return $this->createdDate;
    }

    /**
     * Set source
     *
     * @param integer $source
     * @return User
     */
    public function setSource($source)
    {
        $this->source = $source;

        return $this;
    }

    /**
     * Get source
     *
     * @return integer
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set idInSource
     *
     * @param string $idInSource
     * @return User
     */
    public function setIdInSource($idInSource)
    {
        $this->idInSource = $idInSource;

        return $this;
    }

    /**
     * Get idInSource
     *
     * @return string
     */
    public function getIdInSource()
    {
        return $this->idInSource;
    }

    /**
     * Set socnetInfo
     *
     * @param string $socnetInfo
     * @return User
     */
    public function setSocnetInfo($socnetInfo)
    {
        $this->socnetInfo = $socnetInfo;

        return $this;
    }

    /**
     * Get socnetInfo
     *
     * @return string
     */
    public function getSocnetInfo()
    {
        return $this->socnetInfo;
    }

    /**
     * Set scores
     *
     * @param integer $scores
     * @return User
     */
    public function setScores($scores)
    {
        $this->scores = $scores;

        return $this;
    }

    /**
     * Get scores
     *
     * @return integer
     */
    public function getScores()
    {
        return $this->scores;
    }

    /**
     * Set countersDay
     *
     * @param \DateTime $countersDay
     * @return User
     */
    public function setCountersDay($countersDay)
    {
        $this->countersDay = $countersDay;

        return $this;
    }

    /**
     * Get countersDay
     *
     * @return \DateTime
     */
    public function getCountersDay()
    {
        return $this->countersDay;
    }

    /**
     * Set countQuestionAtDay
     *
     * @param integer $countQuestionAtDay
     * @return User
     */
    public function setCountQuestionAtDay($countQuestionAtDay)
    {
        $this->countQuestionAtDay = $countQuestionAtDay;

        return $this;
    }

    /**
     * Get countQuestionAtDay
     *
     * @return integer
     */
    public function getCountQuestionAtDay()
    {
        return $this->countQuestionAtDay;
    }

    /**
     * Set countAnswerAtDay
     *
     * @param integer $countAnswerAtDay
     * @return User
     */
    public function setCountAnswerAtDay($countAnswerAtDay)
    {
        $this->countAnswerAtDay = $countAnswerAtDay;

        return $this;
    }

    /**
     * Get countAnswerAtDay
     *
     * @return integer
     */
    public function getCountAnswerAtDay()
    {
        return $this->countAnswerAtDay;
    }

    /**
     * Set countPostForeignLinkAtDay
     *
     * @param integer $countPostForeignLinkAtDay
     * @return User
     */
    public function setCountPostForeignLinkAtDay($countPostForeignLinkAtDay)
    {
        $this->countPostForeignLinkAtDay = $countPostForeignLinkAtDay;

        return $this;
    }

    /**
     * Get countPostForeignLinkAtDay
     *
     * @return integer
     */
    public function getCountPostForeignLinkAtDay()
    {
        return $this->countPostForeignLinkAtDay;
    }

    /**
     * Set countPostForeignLinkOnTrustedSiteAtDay
     *
     * @param integer $countPostForeignLinkOnTrustedSiteAtDay
     * @return User
     */
    public function setCountPostForeignLinkOnTrustedSiteAtDay($countPostForeignLinkOnTrustedSiteAtDay)
    {
        $this->countPostForeignLinkOnTrustedSiteAtDay = $countPostForeignLinkOnTrustedSiteAtDay;

        return $this;
    }

    /**
     * Get countPostForeignLinkOnTrustedSiteAtDay
     *
     * @return integer
     */
    public function getCountPostForeignLinkOnTrustedSiteAtDay()
    {
        return $this->countPostForeignLinkOnTrustedSiteAtDay;
    }

    /**
     * Set foto
     *
     * @param \mh\BTBundle\Entity\UserFoto $foto
     * @return User
     */
    public function setFoto(\mh\BTBundle\Entity\UserFoto $foto = null)
    {
        $this->foto = $foto;

        return $this;
    }

    /**
     * Get foto
     *
     * @return \mh\BTBundle\Entity\UserFoto
     */
    public function getFoto()
    {
        return $this->foto;
    }

    /**
     * Add sessions
     *
     * @param \mh\BTBundle\Entity\UserSession $sessions
     * @return User
     */
    public function addSession(\mh\BTBundle\Entity\UserSession $sessions)
    {
        $this->sessions[] = $sessions;

        return $this;
    }

    /**
     * Remove sessions
     *
     * @param \mh\BTBundle\Entity\UserSession $sessions
     */
    public function removeSession(\mh\BTBundle\Entity\UserSession $sessions)
    {
        $this->sessions->removeElement($sessions);
    }

    /**
     * Get sessions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSessions()
    {
        return $this->sessions;
    }

    /**
     * Add posts
     *
     * @param \mh\BTBundle\Entity\Post $posts
     * @return User
     */
    public function addPost(\mh\BTBundle\Entity\Post $posts)
    {
        $this->posts[] = $posts;

        return $this;
    }

    /**
     * Remove posts
     *
     * @param \mh\BTBundle\Entity\Post $posts
     */
    public function removePost(\mh\BTBundle\Entity\Post $posts)
    {
        $this->posts->removeElement($posts);
    }

    /**
     * Get posts
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPosts()
    {
        return $this->posts;
    }

    /**
     * Add answers
     *
     * @param \mh\BTBundle\Entity\Answer $answers
     * @return User
     */
    public function addAnswer(\mh\BTBundle\Entity\Answer $answers)
    {
        $this->answers[] = $answers;

        return $this;
    }

    /**
     * Remove answers
     *
     * @param \mh\BTBundle\Entity\Answer $answers
     */
    public function removeAnswer(\mh\BTBundle\Entity\Answer $answers)
    {
        $this->answers->removeElement($answers);
    }

    /**
     * Get answers
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getAnswers()
    {
        return $this->answers;
    }

    /**
     * Add questions
     *
     * @param \mh\BTBundle\Entity\Question $questions
     * @return User
     */
    public function addQuestion(\mh\BTBundle\Entity\Question $questions)
    {
        $this->questions[] = $questions;

        return $this;
    }

    /**
     * Remove questions
     *
     * @param \mh\BTBundle\Entity\Question $questions
     */
    public function removeQuestion(\mh\BTBundle\Entity\Question $questions)
    {
        $this->questions->removeElement($questions);
    }

    /**
     * Get questions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getQuestions()
    {
        return $this->questions;
    }

    /**
     * Add postComments
     *
     * @param \mh\BTBundle\Entity\PostComment $postComments
     * @return User
     */
    public function addPostComment(\mh\BTBundle\Entity\PostComment $postComments)
    {
        $this->postComments[] = $postComments;

        return $this;
    }

    /**
     * Remove postComments
     *
     * @param \mh\BTBundle\Entity\PostComment $postComments
     */
    public function removePostComment(\mh\BTBundle\Entity\PostComment $postComments)
    {
        $this->postComments->removeElement($postComments);
    }

    /**
     * Get postComments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPostComments()
    {
        return $this->postComments;
    }

    /**
     * Set currentSession
     *
     * @param \mh\BTBundle\Entity\UserSession $currentSession
     * @return User
     */
    public function setCurrentSession(\mh\BTBundle\Entity\UserSession $currentSession = null)
    {
        $this->currentSession = $currentSession;

        return $this;
    }

    /**
     * Get currentSession
     *
     * @return \mh\BTBundle\Entity\UserSession
     */
    public function getCurrentSession()
    {
        return $this->currentSession;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->sessions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->posts = new \Doctrine\Common\Collections\ArrayCollection();
        $this->answers = new \Doctrine\Common\Collections\ArrayCollection();
        $this->questions = new \Doctrine\Common\Collections\ArrayCollection();
        $this->postComments = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Set password
     *
     * @param string $password
     * @return User
     */
    public function setPassword($password)
    {
        $this->password = $password;

        return $this;
    }
    /**
     * @var string
     */
    private $inviteCode;


    /**
     * Set inviteCode
     *
     * @param string $inviteCode
     * @return User
     */
    public function setInviteCode($inviteCode)
    {
        $this->inviteCode = $inviteCode;

        return $this;
    }

    /**
     * Get inviteCode
     *
     * @return string
     */
    public function getInviteCode()
    {
        return $this->inviteCode;
    }
    /**
     * @var \mh\BTBundle\Entity\User
     */
    private $invitingUser;


    /**
     * Set invitingUser
     *
     * @param \mh\BTBundle\Entity\User $invitingUser
     * @return User
     */
    public function setInvitingUser(\mh\BTBundle\Entity\User $invitingUser = null)
    {
        $this->invitingUser = $invitingUser;

        return $this;
    }

    /**
     * Get invitingUser
     *
     * @return \mh\BTBundle\Entity\User
     */
    public function getInvitingUser()
    {
        return $this->invitingUser;
    }
}