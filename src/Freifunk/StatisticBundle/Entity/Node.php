<?php

namespace Freifunk\StatisticBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Node
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Freifunk\StatisticBundle\Entity\NodeRepository")
 */
class Node
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
     * @var string
     *
     * @ORM\Column(name="nodeName", type="string", length=255)
     */
    private $nodeName;

    /**
     * @var string
     *
     * @ORM\Column(name="realName", type="string", length=255)
     */
    private $realName;

    /**
     * @var string
     *
     * @ORM\Column(name="mac", type="string", length=17)
     */
    private $mac;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float")
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float")
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="fastdKey", type="string", length=64)
     */
    private $fastdKey;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="NodeStat", mappedBy="node", fetch="EXTRA_LAZY")
     */
    private $stats;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    private $createdAt;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stats = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set nodeName
     *
     * @param string $nodeName
     * @return Node
     */
    public function setNodeName($nodeName)
    {
        $this->nodeName = $nodeName;
    
        return $this;
    }

    /**
     * Get nodeName
     *
     * @return string 
     */
    public function getNodeName()
    {
        return $this->nodeName;
    }

    /**
     * Set realName
     *
     * @param string $realName
     * @return Node
     */
    public function setRealName($realName)
    {
        $this->realName = $realName;
    
        return $this;
    }

    /**
     * Get realName
     *
     * @return string 
     */
    public function getRealName()
    {
        return $this->realName;
    }

    /**
     * Set mac
     *
     * @param string $mac
     * @return Node
     */
    public function setMac($mac)
    {
        $this->mac = $mac;
    
        return $this;
    }

    /**
     * Get mac
     *
     * @return string 
     */
    public function getMac()
    {
        return $this->mac;
    }

    /**
     * Set latitude
     *
     * @param float $latitude
     * @return Node
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    
        return $this;
    }

    /**
     * Get latitude
     *
     * @return float 
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Set longitude
     *
     * @param float $longitude
     * @return Node
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    
        return $this;
    }

    /**
     * Get longitude
     *
     * @return float 
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Set fastdKey
     *
     * @param string $fastdKey
     * @return Node
     */
    public function setFastdKey($fastdKey)
    {
        $this->fastdKey = $fastdKey;
    
        return $this;
    }

    /**
     * Get fastdKey
     *
     * @return string 
     */
    public function getFastdKey()
    {
        return $this->fastdKey;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Node
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
     * Add stats
     *
     * @param \Freifunk\StatisticBundle\Entity\NodeStat $stats
     * @return Node
     */
    public function addStat(\Freifunk\StatisticBundle\Entity\NodeStat $stats)
    {
        $this->stats[] = $stats;
    
        return $this;
    }

    /**
     * Remove stats
     *
     * @param \Freifunk\StatisticBundle\Entity\NodeStat $stats
     */
    public function removeStat(\Freifunk\StatisticBundle\Entity\NodeStat $stats)
    {
        $this->stats->removeElement($stats);
    }

    /**
     * Get stats
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStats()
    {
        return $this->stats;
    }
}