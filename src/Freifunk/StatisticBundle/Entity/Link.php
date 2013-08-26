<?php

namespace Freifunk\StatisticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Link
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Freifunk\StatisticBundle\Entity\LinkRepository")
 */
class Link {

    const VPN = 0;
    const CLIENT = 1;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="type", type="integer")
     */
    private $type;

    /**
     * @var Node
     *
     * @ORM\ManyToOne(targetEntity="Node", fetch="EAGER")
     */
    private $source;

    /**
     * @var Node
     *
     * @ORM\ManyToOne(targetEntity="Node", fetch="EAGER")
     */
    private $target;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    public function __construct()
    {
        $this->createdAt = new \DateTime();
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
     * Set type
     *
     * @param integer $type
     * @return Link
     */
    public function setType($type)
    {
        $this->type = $type;
    
        return $this;
    }

    /**
     * Get type
     *
     * @return integer 
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Link
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;
    
        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set source
     *
     * @param \Freifunk\StatisticBundle\Entity\Node $source
     * @return Link
     */
    public function setSource(\Freifunk\StatisticBundle\Entity\Node $source = null)
    {
        $this->source = $source;
    
        return $this;
    }

    /**
     * Get source
     *
     * @return \Freifunk\StatisticBundle\Entity\Node 
     */
    public function getSource()
    {
        return $this->source;
    }

    /**
     * Set target
     *
     * @param \Freifunk\StatisticBundle\Entity\Node $target
     * @return Link
     */
    public function setTarget(\Freifunk\StatisticBundle\Entity\Node $target = null)
    {
        $this->target = $target;
    
        return $this;
    }

    /**
     * Get target
     *
     * @return \Freifunk\StatisticBundle\Entity\Node 
     */
    public function getTarget()
    {
        return $this->target;
    }
}