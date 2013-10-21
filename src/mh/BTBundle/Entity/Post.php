<?php

namespace mh\BTBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use mh\BTBundle\DBAL\ModerationStatusType;

/**
 * Post
 */
class Post extends Base\ContentObjectBase
{
    protected  $contentType = 'post';

	/**
     * @ORM\PrePersist
     */
    public function prePersist()
    {
		$this->setShowOnMain(false);
		$this->setCreatedDate(new \DateTime());
        $this->setModerationStatus(ModerationStatusType::NOT_MODERATED);
        if (!$this->getScores()) $this->setScores(0);

        parent::prePersist();
    }

    public function getTagsString()
    {
        $ids = array();
        foreach ($this->getTags() as $tag) {
            $ids[] = $tag->getLabel();
        }
        return implode(', ', $ids);
    }

    public function __toString()
    {
        return $this->getTitle();
    }

	public function getPreview()
    {
		$image = $this->getImage() ? $this->getImage() : new PostImage();
		return $image->getBrowserPath();
    }

	public function getTextPreview()
	{
		if ($this->getSubtitle()) {
			return $this->getSubtitle();
		}
		return mb_substr($this->getClearContent(), 0, 100, 'UTF-8');
	}
	
	public static function clearContent($text)
	{
		$text = strip_tags($text);
		return preg_replace("/&#?[a-z0-9]+;/i", " ", $text);
	}

	public function getClearContent()
	{
		return self::clearContent($this->getContent());
	}

	public function getClearContentLength()
	{
		return mb_strlen($this->getClearContent(),'UTF-8');
	}

	public function getAttachedImageCount()
	{
		preg_match_all('/<img[^>]+>/i', $this->getContent(), $result);
		return $result ? count($result[0]) : 0;
	}

    /*--------------------------------------------------------------------------*/


    /**
     * @var integer
     */
    private $id;

    /**
     * @var string
     */
    private $slug;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $subtitle;

    /**
     * @var string
     */
    private $content;

    /**
     * @var \DateTime
     */
    private $createdDate;

    /**
     * @var integer
     */
    private $baseRate;

    /**
     * @var ModerationStatus
     */
    private $moderationStatus;

    /**
     * @var boolean
     */
    private $isPublished;

    /**
     * @var boolean
     */
    private $showOnMain;

    /**
     * @var \mh\BTBundle\Entity\ContentObject
     */
    private $contentObject;

    /**
     * @var \mh\BTBundle\Entity\PostImage
     */
    private $image;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $comments;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $foreignLinks;

    /**
     * @var \mh\BTBundle\Entity\User
     */
    private $user;

    /**
     * @var \mh\BTBundle\Entity\Tender
     */
    private $tender;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $tags;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $categories;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->comments = new \Doctrine\Common\Collections\ArrayCollection();
        $this->foreignLinks = new \Doctrine\Common\Collections\ArrayCollection();
        $this->tags = new \Doctrine\Common\Collections\ArrayCollection();
        $this->categories = new \Doctrine\Common\Collections\ArrayCollection();
	}

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
     * Set slug
     *
     * @param string $slug
     * @return Post
     */
    public function setSlug($slug)
    {
        $this->slug = $slug;

        return $this;
    }

    /**
     * Get slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->slug;
    }

    /**
     * Set title
     *
     * @param string $title
     * @return Post
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set subtitle
     *
     * @param string $subtitle
     * @return Post
     */
    public function setSubtitle($subtitle)
    {
        $this->subtitle = $subtitle;

        return $this;
    }

    /**
     * Get subtitle
     *
     * @return string
     */
    public function getSubtitle()
    {
        return $this->subtitle;
    }

    /**
     * Set content
     *
     * @param string $content
     * @return Post
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdDate
     *
     * @param \DateTime $createdDate
     * @return Post
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
     * Set baseRate
     *
     * @param integer $baseRate
     * @return Post
     */
    public function setBaseRate($baseRate)
    {
        $this->baseRate = $baseRate;

        return $this;
    }

    /**
     * Get baseRate
     *
     * @return integer
     */
    public function getBaseRate()
    {
        return $this->baseRate;
    }

    /**
     * Set moderationStatus
     *
     * @param ModerationStatus $moderationStatus
     * @return Post
     */
    public function setModerationStatus($moderationStatus)
    {
        $this->moderationStatus = $moderationStatus;

        return $this;
    }

    /**
     * Get moderationStatus
     *
     * @return ModerationStatus
     */
    public function getModerationStatus()
    {
        return $this->moderationStatus;
    }

    /**
     * Set isPublished
     *
     * @param boolean $isPublished
     * @return Post
     */
    public function setIsPublished($isPublished)
    {
        $this->isPublished = $isPublished;

        return $this;
    }

    /**
     * Get isPublished
     *
     * @return boolean
     */
    public function getIsPublished()
    {
        return $this->isPublished;
    }

    /**
     * Set showOnMain
     *
     * @param boolean $showOnMain
     * @return Post
     */
    public function setShowOnMain($showOnMain)
    {
        $this->showOnMain = $showOnMain;

        return $this;
    }

    /**
     * Get showOnMain
     *
     * @return boolean
     */
    public function getShowOnMain()
    {
        return $this->showOnMain;
    }

    /**
     * Set contentObject
     *
     * @param \mh\BTBundle\Entity\ContentObject $contentObject
     * @return Post
     */
    public function setContentObject(\mh\BTBundle\Entity\ContentObject $contentObject = null)
    {
        $this->contentObject = $contentObject;

        return $this;
    }

    /**
     * Get contentObject
     *
     * @return \mh\BTBundle\Entity\ContentObject
     */
    public function getContentObject()
    {
        return $this->contentObject;
    }

    /**
     * Set image
     *
     * @param \mh\BTBundle\Entity\PostImage $image
     * @return Post
     */
    public function setImage(\mh\BTBundle\Entity\PostImage $image = null)
    {
        $this->image = $image;

        return $this;
    }

    /**
     * Get image
     *
     * @return \mh\BTBundle\Entity\PostImage
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * Add comments
     *
     * @param \mh\BTBundle\Entity\PostComment $comments
     * @return Post
     */
    public function addComment(\mh\BTBundle\Entity\PostComment $comments)
    {
        $this->comments[] = $comments;

        return $this;
    }

    /**
     * Remove comments
     *
     * @param \mh\BTBundle\Entity\PostComment $comments
     */
    public function removeComment(\mh\BTBundle\Entity\PostComment $comments)
    {
        $this->comments->removeElement($comments);
    }

    /**
     * Get comments
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getComments()
    {
        return $this->comments;
    }

    /**
     * Add foreignLinks
     *
     * @param \mh\BTBundle\Entity\PostForeignLink $foreignLinks
     * @return Post
     */
    public function addForeignLink(\mh\BTBundle\Entity\PostForeignLink $foreignLinks)
    {
        $this->foreignLinks[] = $foreignLinks;

        return $this;
    }

    /**
     * Remove foreignLinks
     *
     * @param \mh\BTBundle\Entity\PostForeignLink $foreignLinks
     */
    public function removeForeignLink(\mh\BTBundle\Entity\PostForeignLink $foreignLinks)
    {
        $this->foreignLinks->removeElement($foreignLinks);
    }

    /**
     * Get foreignLinks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getForeignLinks()
    {
        return $this->foreignLinks;
    }

    /**
     * Set user
     *
     * @param \mh\BTBundle\Entity\User $user
     * @return Post
     */
    public function setUser(\mh\BTBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \mh\BTBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set tender
     *
     * @param \mh\BTBundle\Entity\Tender $tender
     * @return Post
     */
    public function setTender(\mh\BTBundle\Entity\Tender $tender = null)
    {
        $this->tender = $tender;

        return $this;
    }

    /**
     * Get tender
     *
     * @return \mh\BTBundle\Entity\Tender
     */
    public function getTender()
    {
        return $this->tender;
    }

    /**
     * Add tags
     *
     * @param \mh\BTBundle\Entity\Tag $tags
     * @return Post
     */
    public function addTag(\mh\BTBundle\Entity\Tag $tags)
    {
        $this->tags[] = $tags;

        return $this;
    }

    /**
     * Remove tags
     *
     * @param \mh\BTBundle\Entity\Tag $tags
     */
    public function removeTag(\mh\BTBundle\Entity\Tag $tags)
    {
        $this->tags->removeElement($tags);
    }

    /**
     * Get tags
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Add categories
     *
     * @param \mh\BTBundle\Entity\Category $categories
     * @return Post
     */
    public function addCategorie(\mh\BTBundle\Entity\Category $categories)
    {
        $this->categories[] = $categories;

        return $this;
    }

    /**
     * Remove categories
     *
     * @param \mh\BTBundle\Entity\Category $categories
     */
    public function removeCategorie(\mh\BTBundle\Entity\Category $categories)
    {
        $this->categories->removeElement($categories);
    }

    /**
     * Get categories
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCategories()
    {
        return $this->categories;
    }
    /**
     * @var \mh\BTBundle\Entity\ScoreObject
     */
    private $scoreObject;


    /**
     * Set scoreObject
     *
     * @param \mh\BTBundle\Entity\ScoreObject $scoreObject
     * @return Post
     */
    public function setScoreObject(\mh\BTBundle\Entity\ScoreObject $scoreObject = null)
    {
        $this->scoreObject = $scoreObject;
    
        return $this;
    }

    /**
     * Get scoreObject
     *
     * @return \mh\BTBundle\Entity\ScoreObject 
     */
    public function getScoreObject()
    {
        return $this->scoreObject;
    }
}