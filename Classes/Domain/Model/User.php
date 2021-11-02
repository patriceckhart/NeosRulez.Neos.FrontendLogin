<?php
namespace NeosRulez\Neos\FrontendLogin\Domain\Model;

/*
 * This file is part of the NeosRulez.Neos.FrontendLogin package.
 */

use Neos\Flow\Annotations as Flow;
use Doctrine\ORM\Mapping as ORM;

/**
 * @Flow\Entity
 */
class User
{

    /**
     * @var \Neos\Neos\Domain\Model\User
     * @ORM\OneToOne(cascade={"persist", "remove"})
     */
    protected $user;

    /**
     * @return \Neos\Neos\Domain\Model\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param \Neos\Neos\Domain\Model\User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
    }

    /**
     * @var string
     * @ORM\Column(type="text")
     * @ORM\Column(length=9000)
     */
    protected $properties;

    /**
     * @return array
     */
    public function getProperties()
    {
        return json_decode($this->properties, true);
    }

    /**
     * @param string $propertyName
     * @return mixed
     */
    public function getProperty(string $propertyName)
    {
        $result = false;
        if(array_key_exists($propertyName, $this->getProperties())) {
            $result = $this->getProperties()[$propertyName];
        }
        return $result;
    }

    /**
     * @param string $properties
     * @return void
     */
    public function setProperties($properties)
    {
        $this->properties = $properties;
    }

    /**
     * @var boolean
     */
    protected $active = true;

    /**
     * @return boolean
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param boolean $active
     * @return void
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @var \DateTime
     */
    protected $created;

    public function __construct() {
        $this->created = new \DateTime();
    }

    /**
     * @return \DateTime
     */
    public function getCreated() {
        return $this->created;
    }

    /**
     * @var \DateTime
     */
    protected $modified;

    /**
     * @return \DateTime
     */
    public function getModified() {
        return $this->modified;
    }

    /**
     * @param \DateTime $modified
     * @return void
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    }

}
