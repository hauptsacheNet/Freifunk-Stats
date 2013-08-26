<?php

namespace Freifunk\StatisticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * UpdateLog
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Freifunk\StatisticBundle\Entity\UpdateLogRepository")
 */
class UpdateLog
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fileTime", type="datetime")
     */
    private $fileTime;

    /**
     * @var integer
     *
     * @ORM\Column(name="nodesAdded", type="integer")
     */
    private $nodesAdded;

    /**
     * @var integer
     *
     * @ORM\Column(name="nodesRemoved", type="integer")
     */
    private $nodesRemoved;

    /**
     * @var integer
     *
     * @ORM\Column(name="nodesPreserved", type="integer")
     */
    private $nodesPreserved;

    /**
     * @var integer
     *
     * @ORM\Column(name="statusUpdates", type="integer")
     */
    private $statusUpdates;

    /**
     * @var integer
     *
     * @ORM\Column(name="linksAdded", type="integer")
     */
    private $linksAdded;

    /**
     * @var integer
     *
     * @ORM\Column(name="linksRemoved", type="integer")
     */
    private $linksRemoved;

    /**
     * @var integer
     *
     * @ORM\Column(name="linksPreserved", type="integer")
     */
    private $linksPreserved;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message;

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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return UpdateLog
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
     * Set fileTime
     *
     * @param \DateTime $fileTime
     * @return UpdateLog
     */
    public function setFileTime($fileTime)
    {
        $this->fileTime = $fileTime;
    
        return $this;
    }

    /**
     * Get fileTime
     *
     * @return \DateTime 
     */
    public function getFileTime()
    {
        return $this->fileTime;
    }

    /**
     * Set nodesAdded
     *
     * @param integer $nodesAdded
     * @return UpdateLog
     */
    public function setNodesAdded($nodesAdded)
    {
        $this->nodesAdded = $nodesAdded;
    
        return $this;
    }

    /**
     * Get nodesAdded
     *
     * @return integer 
     */
    public function getNodesAdded()
    {
        return $this->nodesAdded;
    }

    /**
     * Set nodesRemoved
     *
     * @param integer $nodesRemoved
     * @return UpdateLog
     */
    public function setNodesRemoved($nodesRemoved)
    {
        $this->nodesRemoved = $nodesRemoved;
    
        return $this;
    }

    /**
     * Get nodesRemoved
     *
     * @return integer 
     */
    public function getNodesRemoved()
    {
        return $this->nodesRemoved;
    }

    /**
     * Set nodesPreserved
     *
     * @param integer $nodesPreserved
     * @return UpdateLog
     */
    public function setNodesPreserved($nodesPreserved)
    {
        $this->nodesPreserved = $nodesPreserved;
    
        return $this;
    }

    /**
     * Get nodesPreserved
     *
     * @return integer 
     */
    public function getNodesPreserved()
    {
        return $this->nodesPreserved;
    }

    /**
     * Set statusUpdates
     *
     * @param integer $statusUpdates
     * @return UpdateLog
     */
    public function setStatusUpdates($statusUpdates)
    {
        $this->statusUpdates = $statusUpdates;
    
        return $this;
    }

    /**
     * Get statusUpdates
     *
     * @return integer 
     */
    public function getStatusUpdates()
    {
        return $this->statusUpdates;
    }

    /**
     * Set linksAdded
     *
     * @param integer $linksAdded
     * @return UpdateLog
     */
    public function setLinksAdded($linksAdded)
    {
        $this->linksAdded = $linksAdded;
    
        return $this;
    }

    /**
     * Get linksAdded
     *
     * @return integer 
     */
    public function getLinksAdded()
    {
        return $this->linksAdded;
    }

    /**
     * Set linksRemoved
     *
     * @param integer $linksRemoved
     * @return UpdateLog
     */
    public function setLinksRemoved($linksRemoved)
    {
        $this->linksRemoved = $linksRemoved;
    
        return $this;
    }

    /**
     * Get linksRemoved
     *
     * @return integer 
     */
    public function getLinksRemoved()
    {
        return $this->linksRemoved;
    }

    /**
     * Set linksPreserved
     *
     * @param integer $linksPreserved
     * @return UpdateLog
     */
    public function setLinksPreserved($linksPreserved)
    {
        $this->linksPreserved = $linksPreserved;
    
        return $this;
    }

    /**
     * Get linksPreserved
     *
     * @return integer 
     */
    public function getLinksPreserved()
    {
        return $this->linksPreserved;
    }

    /**
     * Set message
     *
     * @param string $message
     * @return UpdateLog
     */
    public function setMessage($message)
    {
        $this->message = $message;
    
        return $this;
    }

    /**
     * Get message
     *
     * @return string 
     */
    public function getMessage()
    {
        return $this->message;
    }
}