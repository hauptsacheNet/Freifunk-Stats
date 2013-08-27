<?php

namespace Freifunk\StatisticBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

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
     * @ORM\Column(name="nodeName", type="string", length=32, nullable=true)
     * @Assert\Length(min=1,max=32)
     * @Assert\Regex("/^[-a-zA-Z0-9_]*$/")
     */
    private $nodeName;

    /**
     * @var string
     *
     * @ORM\Column(name="realName", type="string", length=64, nullable=true)
     * //@Assert\NotNull
     * @Assert\Length(min=1,max=64)
     * @Assert\Regex("/^[-a-zA-Z0-9_ äöüÄÖÜß]*$/")
     */
    private $realName;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=true)
     * //@Assert\NotNull
     * @Assert\Email
     * @Assert\Length(max=255)
     */
    private $email;

    /**
     * @var string
     *
     * @ORM\Column(name="mac", type="string", length=17, unique=true)
     * @Assert\NotNull
     * @Assert\Regex("/^([a-fA-F0-9]{12}|([a-fA-F0-9]{2}:){5}[a-fA-F0-9]{2})$/")
     * @Assert\Length(max=17)
     */
    private $mac;

    /**
     * @var float
     *
     * @ORM\Column(name="latitude", type="float", nullable=true)
     * @Assert\Range(min=-90, max=90)
     */
    private $latitude;

    /**
     * @var float
     *
     * @ORM\Column(name="longitude", type="float", nullable=true)
     * @Assert\Range(min=-180, max=180)
     */
    private $longitude;

    /**
     * @var string
     *
     * @ORM\Column(name="fastdKey", type="string", length=64, nullable=true)
     * @Assert\Regex("/^[a-fA-F0-9]*$/")
     * @Assert\Length(min=64, max=64)
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
     * @Assert\NotNull
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
     * Set email
     *
     * @param string $email
     * @return Node
     */
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get email
     *
     * @return string
     */
    public function getEmail()
    {
        return $this->email;
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