<?php

namespace mh\BTBundle\Entity\Base;

/**
 * ScoreObjectBase
 */
class ScoreObjectBase
{
    private function sd()
    {
        $obj = new \mh\BTBundle\Entity\ScoreObject();
        $obj->setObjectType($this->objectType);
        $obj->setUser($this->getUser());
        $obj->setCreatedDate($this->getCreatedDate());
        $this->setScoreObject($obj);
    }

    public function prePersist()
    {
        $this->sd();
    }

    public function getScores()
    {
        if ($this->getScoreObject() === NULL) {
            return -1;
        }
        return $this->getScoreObject()->getScores();
    }

    public function setScores($scores)
    {
        if ( ! $this->getScoreObject()) {
            $this->sd();
        }
        $this->getScoreObject()->setScores($scores);

        return $this;
    }
}