<?php

namespace mh\BTBundle\Entity\Base;

/**
 * ScoreObjectBase
 */
class ScoreObjectBase
{
    public function prePersist()
    {
        $this->sd();
    }

    private function sd()
    {
        $className = get_class($this);
        $className = substr($className, strrpos($className, '\\') + 1);

        $obj = new \mh\BTBundle\Entity\ScoreObject();
        $obj->setObjectType($className);
        $obj->setUser($this->getUser());
        $obj->setCreatedDate($this->getCreatedDate());
        $obj->setScores(10);

        $this->setScoreObject($obj);
    }

    public function getScores()
    {
        if ($this->getScoreObject() === NULL) {
            return 0;
        }
        return $this->getScoreObject()->getScores();
    }

    public function setScores($scores)
    {
        if ($this->getScoreObject() === NULL) {
            $this->sd();
        }
        $this->getScoreObject()->setScores($scores);

        return $this;
    }
}