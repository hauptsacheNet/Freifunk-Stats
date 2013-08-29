<?php

namespace Freifunk\StatisticBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * NodeStat
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Freifunk\StatisticBundle\Entity\NodeStatRepository")
 */
class NodeStat
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
     * @var boolean
     *
     * @ORM\Column(name="online", type="boolean")
     * @Assert\NotNull
     */
    private $online;

    /**
     * @var integer
     *
     * @ORM\Column(name="clientCount", type="integer")
     * @Assert\Range(min=0, max=2147483647)
     */
    private $clientCount;

    /**
     * @var Node
     *
     * @ORM\ManyToOne(targetEntity="Node", inversedBy="stats", fetch="LAZY")
     * @Assert\NotNull
     */
    private $node;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     * @Assert\NotNull
     */
    private $time;

    /**
     * Constructor
     */
    public function __construct()
    {
    }

    /**
     * Compares two `NodeStat` entities
     *
     * @param NodeStat $other
     *
     * @return bool
     */
    public function equals(NodeStat $other)
    {
        return $this->node == $other->node
            && $this->online == $other->online
            && $this->clientCount == $other->clientCount;
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
     * Set online
     *
     * @param boolean $online
     *
     * @return NodeStat
     */
    public function setOnline($online)
    {
        $this->online = $online;

        return $this;
    }

    /**
     * Get online
     *
     * @return boolean
     */
    public function getOnline()
    {
        return $this->online;
    }

    /**
     * Set clientCount
     *
     * @param integer $clientCount
     *
     * @return NodeStat
     */
    public function setClientCount($clientCount)
    {
        $this->clientCount = $clientCount;

        return $this;
    }

    /**
     * Get clientCount
     *
     * @return integer
     */
    public function getClientCount()
    {
        return $this->clientCount;
    }

    /**
     * Set node
     *
     * @param \Freifunk\StatisticBundle\Entity\Node $node
     *
     * @return NodeStat
     */
    public function setNode(\Freifunk\StatisticBundle\Entity\Node $node = null)
    {
        $this->node = $node;

        return $this;
    }

    /**
     * Get node
     *
     * @return \Freifunk\StatisticBundle\Entity\Node
     */
    public function getNode()
    {
        return $this->node;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     *
     * @return NodeStat
     */
    public function setTime($time)
    {
        $this->time = $time;

        return $this;
    }

    /**
     * Get time
     *
     * @return \DateTime 
     */
    public function getTime()
    {
        return $this->time;
    }
}