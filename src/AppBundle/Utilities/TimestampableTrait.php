<?php
/**
 * Created by PhpStorm.
 * User: Splinter
 * Date: 12/04/2016
 * Time: 19:18
 */

namespace AppBundle\Utilities;


use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;

/**
 * Class TimestampableTrait
 * @package AppBundle\Utilities
 * @HasLifecycleCallbacks()
 */
Trait TimestampableTrait {

    /**
     * @var \DateTime
     * @Column(name="created_at",type="datetime",nullable=true)
     */
    protected $createdAt;

    /**
     * @var \DateTime
     * @Column(name="modified_at",type="datetime",nullable=true)
     */
    protected $modifiedAt;

    /**
     * @return \DateTime
     */
    public function getCreatedAt(){
        return $this->createdAt;
    }

    /**
     * @return \DateTime
     */
    public function getModifiedAt(){
        return $this->modifiedAt;
    }

    /**
     * @param \DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt(\DateTime $createdAt){
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @param \DateTime $modifiedAt
     * @return $this
     */
    public function setModifiedAt(\DateTime $modifiedAt){
        $this->modifiedAt = $modifiedAt;
        return $this;
    }

    /**
     * @PrePersist()
     */
    public function onPersist(){
        $this->createdAt = new \DateTime('NOW');
    }

    /**
     * @PreUpdate()
     */
    public function onUpdate(){
        $this->modifiedAt = new \DateTime('NOW');
    }

}