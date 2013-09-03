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
     * //@Assert\Regex("/^[-a-zA-Z0-9_]*$/")
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
     * @ORM\Column(name="mac", type="string", length=40, unique=true)
     * @Assert\NotNull
     * @Assert\Length(max=40)
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
     * @ORM\OneToMany(targetEntity="Link", mappedBy="target", fetch="LAZY", cascade={"remove"}, orphanRemoval=true)
     */
    private $targetLinks;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="Link", mappedBy="source", fetch="LAZY", cascade={"remove"}, orphanRemoval=true)
     */
    private $sourceLinks;

    /**
     * @var ArrayCollection
     *
     * @ORM\OneToMany(targetEntity="NodeStat", mappedBy="node", fetch="EXTRA_LAZY", cascade={"all"}, orphanRemoval=true)
     */
    private $stats;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="time", type="datetime")
     * @Assert\NotNull
     */
    private $time;

    /**
     * Set mac
     *
     * @param string $mac
     *
     * @return Node
     */
    public function setMac($mac)
    {
        $this->mac = sha1(strtoupper($mac));

        return $this;
    }

    /**
     * Generated code from here...
     */

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stats = new \Doctrine\Common\Collections\ArrayCollection();
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
     *
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
     *
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
     *
     * @return Node
     */
    public function setEmail($email)
    {
        $this->email = strtolower($email);

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
     *
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
     *
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
     *
     * @return Node
     */
    public function setFastdKey($fastdKey)
    {
        $this->fastdKey = strtolower($fastdKey);

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
     * Add stats
     *
     * @param \Freifunk\StatisticBundle\Entity\NodeStat $stats
     *
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

    /**
     * Add targetLinks
     *
     * @param \Freifunk\StatisticBundle\Entity\Link $targetLinks
     *
     * @return Node
     */
    public function addTargetLink(\Freifunk\StatisticBundle\Entity\Link $targetLinks)
    {
        $this->targetLinks[] = $targetLinks;

        return $this;
    }

    /**
     * Remove targetLinks
     *
     * @param \Freifunk\StatisticBundle\Entity\Link $targetLinks
     */
    public function removeTargetLink(\Freifunk\StatisticBundle\Entity\Link $targetLinks)
    {
        $this->targetLinks->removeElement($targetLinks);
    }

    /**
     * Get targetLinks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTargetLinks()
    {
        return $this->targetLinks;
    }

    /**
     * Add sourceLinks
     *
     * @param \Freifunk\StatisticBundle\Entity\Link $sourceLinks
     *
     * @return Node
     */
    public function addSourceLink(\Freifunk\StatisticBundle\Entity\Link $sourceLinks)
    {
        $this->sourceLinks[] = $sourceLinks;

        return $this;
    }

    /**
     * Remove sourceLinks
     *
     * @param \Freifunk\StatisticBundle\Entity\Link $sourceLinks
     */
    public function removeSourceLink(\Freifunk\StatisticBundle\Entity\Link $sourceLinks)
    {
        $this->sourceLinks->removeElement($sourceLinks);
    }

    /**
     * Get sourceLinks
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getSourceLinks()
    {
        return $this->sourceLinks;
    }

    /**
     * Set time
     *
     * @param \DateTime $time
     *
     * @return Node
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