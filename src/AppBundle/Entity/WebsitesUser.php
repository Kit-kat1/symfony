<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Users;
use AppBundle\Entity\Websites;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * WebsitesUser
 * @ORM\Entity(repositoryClass="AppBundle\Repository\WebsitesUserRepository")
 * @ORM\Table(name="websites_user")
 */
class WebsitesUser
{
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var boolean
     */
    protected $notify;

    /**
     * @var \AppBundle\Entity\Users
     *
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
     */
    private $user;

    /**
     * @var \AppBundle\Entity\Websites
     *
     * @ORM\ManyToOne(targetEntity="Websites")
     * @ORM\JoinColumn(name="website_id", referencedColumnName="id")
     */
    protected $website;


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
     * Set notify
     *
     * @param boolean $notify
     *
     * @return WebsitesUser
     */
    public function setNotify($notify)
    {
        $this->notify = $notify;

        return $this;
    }

    /**
     * Get notify
     *
     * @return boolean
     */
    public function getNotify()
    {
        return $this->notify;
    }

    /**
     * Set user
     *
     * @param Users $user
     *
     * @return WebsitesUser
     */
    public function setUser(Users $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return Users
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set website
     *
     * @param Websites $website
     *
     * @return WebsitesUser
     */
    public function setWebsite(Websites $website = null)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return Websites
     */
    public function getWebsite()
    {
        return $this->website;
    }
}
