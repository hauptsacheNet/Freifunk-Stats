<?php

namespace Freifunk\StatisticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @Assert\NotNull
     */
    private $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="fileTime", type="datetime")
     * @Assert\NotNull
     */
    private $fileTime;

    /**
     * @var int
     *
     * @ORM\Column(name="fileSize", type="integer")
     * @Assert\NotNull
     * @Assert\Range(min=0, max=2147483647)
     */
    private $fileSize = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nodesAdded", type="integer")
     * @Assert\NotNull
     * @Assert\Range(min=0, max=2147483647)
     */
    private $nodesAdded = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nodesRemoved", type="integer")
     * @Assert\NotNull
     * @Assert\Range(min=0, max=2147483647)
     */
    private $nodesRemoved = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="nodesPreserved", type="integer")
     * @Assert\NotNull
     * @Assert\Range(min=0, max=2147483647)
     */
    private $nodesPreserved = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="statusUpdates", type="integer")
     * @Assert\NotNull
     * @Assert\Range(min=0, max=2147483647)
     */
    private $statusUpdates = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="linksAdded", type="integer")
     * @Assert\NotNull
     * @Assert\Range(min=0, max=2147483647)
     */
    private $linksAdded = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="linksRemoved", type="integer")
     * @Assert\NotNull
     * @Assert\Range(min=0, max=2147483647)
     */
    private $linksRemoved = 0;

    /**
     * @var integer
     *
     * @ORM\Column(name="linksPreserved", type="integer")
     * @Assert\NotNull
     * @Assert\Range(min=0, max=2147483647)
     */
    private $linksPreserved = 0;

    /**
     * @var string
     *
     * @ORM\Column(name="message", type="text")
     */
    private $message = "";


    public function __construct()
    {
        $this->createdAt = new \DateTime();
        $this->fileTime = new \DateTime();
    }

    public function addMessage($message)
    {
        $this->message .= $message . "\n";
    }

    public function nodePreserved()
    {
        $this->nodesPreserved++;
    }

    public function nodeAdded()
    {
        $this->nodesAdded++;
    }

    public function nodeRemoved()
    {
        $this->nodesRemoved++;
    }

    public function linkPreserved()
    {
        $this->linksPreserved++;
    }

    public function linkAdded()
    {
        $this->linksAdded++;
    }

    public function linkRemoved()
    {
        $this->linksRemoved++;
    }

    public function __toString()
    {
        return 'nodes(new: ' . $this->getNodesAdded() . ', preserved: ' . $this->getNodesPreserved() . ', removed: ' . $this->getNodesRemoved() . ')'
        . "\n" . 'links(new: ' . $this->getLinksAdded() . ', preserved: ' . $this->getLinksPreserved() . ', removed: ' . $this->getLinksRemoved() . ')';
    }

    /**
     * Generated from here...
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

    /**
     * Set fileSize
     *
     * @param integer $fileSize
     * @return UpdateLog
     */
    public function setFileSize($fileSize)
    {
        $this->fileSize = $fileSize;

        return $this;
    }

    /**
     * Get fileSize
     *
     * @return integer
     */
    public function getFileSize()
    {
        return $this->fileSize;
    }
}