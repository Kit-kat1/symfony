<?php

namespace AppBundle\Entity;

use AppBundle\Entity\Users;
use AppBundle\Entity\Websites;
/**
 * WebsitesUser
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
     * @var Websites
     */
    protected $user;

    /**
     * @var Websites
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

