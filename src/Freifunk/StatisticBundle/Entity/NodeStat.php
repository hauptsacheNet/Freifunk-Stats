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
     * @ORM\Column(name="createdAt", type="datetime")
     * @Assert\NotNull
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
     * Set online
     *
     * @param boolean $online
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
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return NodeStat
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
     * Set node
     *
     * @param \Freifunk\StatisticBundle\Entity\Node $node
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
}