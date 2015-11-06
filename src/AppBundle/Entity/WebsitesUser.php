<?php

namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * WebsitesUser
 */
class WebsitesUser
{
    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
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
    protected $user;

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
     * @param \AppBundle\Entity\Users $user
     *
     * @return WebsitesUser
     */
    public function setUser(\AppBundle\Entity\Users $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \AppBundle\Entity\Users
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set website
     *
     * @param \AppBundle\Entity\Websites $website
     *
     * @return WebsitesUser
     */
    public function setWebsite(\AppBundle\Entity\Websites $website = null)
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Get website
     *
     * @return \AppBundle\Entity\Websites
     */
    public function getWebsite()
    {
        return $this->website;
    }
}





