<?php

namespace mh\BTBundle\Entity\Base;

/**
 * ContentObjectBase
 */
class ContentObjectBase extends ScoreObjectBase
{
    public function prePersist()
    {
        $obj = new \mh\BTBundle\Entity\ContentObject();
	    $obj->setComplaintsCount(0);
		$obj->setLikesCount(0);
        $obj->setObjectType($this->objectType);
        $this->setContentObject($obj);

		$this->setIsPublished(true);
	}

    public function incComplaintsCount($v = 1)
    {
        $this->setComplaintsCount($this->getComplaintsCount() + $v);
    }

    public function decComplaintsCount($v = 1)
    {
        $this->setComplaintsCount($this->getComplaintsCount() - $v);
    }

	public function incLikesCount($v = 1)
    {
        $this->setLikesCount($this->getLikesCount() + $v);
    }

    public function decLikesCount($v = 1)
    {
        $this->setLikesCount($this->getLikesCount() - $v);
    }

    public function getLikesCount()
    {
        return $this->getContentObject()->getLikesCount();
    }

}