<?php
namespace AppBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\User as BaseUser;
use JMS\Serializer\Annotation\ExclusionPolicy;
use JMS\Serializer\Annotation\Expose;
use JMS\Serializer\Annotation\Groups;
use JMS\Serializer\Annotation\VirtualProperty;

/**
 * Users
 * @ORM\Entity(repositoryClass="AppBundle\Repository\UsersRepository")
 * @ORM\Table(name="users")
 * @ExclusionPolicy("all")
 */
class Users extends BaseUser
{
    
    /**
     * @var integer
     * @Expose
     */
    protected $id;

    /**
     * @var string
     * @Expose
     */
    protected $firstName;

    /**
     * @var string
     * @Expose
     */
    protected $lastName;
//
//    /**
//     * @var string
//     */
//    protected $active = 'disabled';

    /**
     * @var integer
     * @Expose
     */
    protected $phoneNumber;

    /**
     * @var \DateTime
     * @Expose
     */
    protected $created;

    /**
     * @var \DateTime
     * @Expose
     */
    protected $updated;

//    /**
//     * @OneToOne(targetEntity="Roles")
//     * @JoinColumn(name="roleId", referencedColumnName="id")
//     * @var integer
//     */
//    protected $roleid;


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
     * Set firstName
     *
     * @param string $firstName
     *
     * @return Users
     */
    public function setFirstName($firstName)
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function set()
    {
        return 'qq';
    }

    /**
     * Set salt
     *
     * @param string $salt
     *
     * @return Users
     */
    public function setSalt($salt)
    {
        $this->salt = $salt;

        return $this;
    }

    /**
     * Get firstName
     *
     * @return string
     */
    public function getFirstName()
    {
        return $this->firstName;
    }

    /**
     * Set lastName
     *
     * @param string $lastName
     *
     * @return Users
     */
    public function setLastName($lastName)
    {
        $this->lastName = $lastName;

        return $this;
    }

    /**
     * Get lastName
     *
     * @return string
     */
    public function getLastName()
    {
        return $this->lastName;
    }

//    /**
//     * Set active
//     *
//     * @param string $active
//     *
//     * @return Users
//     */
//    public function setActive($active)
//    {
//        $this->active = $active;
//
//        return $this;
//    }
//
//    /**
//     * Get active
//     *
//     * @return string
//     */
//    public function getActive()
//    {
//        return $this->active;
//    }

    /**
     * Set phoneNumber
     *
     * @param integer $phoneNumber
     *
     * @return Users
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return integer
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set created
     *
     * @param \DateTime $created
     *
     * @return Users
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

    /**
     * Get created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Set updated
     *
     * @param \DateTime $updated
     *
     * @return Users
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Get updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

//    /**
//     * Set roleid
//     *
//     * @return Users
//     */
//    public function setRoleId($roleId)
//    {
//        $this->roleid = $roleId;
//
//        return $this;
//    }
//
//    /**
//     * Get roleid
//     *
//     * @return integer
//     */
//    public function getRoleId()
//    {
//        return $this->roleid;
//    }

    public function eraseCredentials()
    {

    }

//    /**
//     * @param UserInterface $user
//     * @return boolean
//     */
//    public function equals(UserInterface $user)
//    {
//        return md5($this->getUsername()) == md5($user->getUsername());
//    }

    public function __construct()
    {
        $this->created = new \DateTime();
        parent::__construct();
    }

    /**
     * Serializes the user.
     *
     * The serialized data have to contain the fields used during check for
     * changes and the id.
     *
     * @return string
     */
    public function serialize()
    {
        return serialize(array(
            $this->password,
            $this->salt,
            $this->firstName,
            $this->username,
//            $this->active,
            $this->lastName,
            $this->phoneNumber,
            $this->updated,
            $this->id,
            $this->created,
//            $this->roleid,
            $this->roles,
            $this->email,
        ));
    }

    /**
     * Unserializes the user.
     *
     * @param string $serialized
     */
    public function unserialize($serialized)
    {
        $data = unserialize($serialized);
        // add a few extra elements in the array to ensure that we have enough keys when unserializing
        // older data which does not include all properties.
        $data = array_merge($data, array_fill(0, 2, null));

        list(
            $this->password,
            $this->salt,
            $this->firstName,
            $this->username,
//            $this->active,
            $this->lastName,
            $this->phoneNumber,
            $this->updated,
            $this->id,
            $this->created,
            $this->roles,
//            $this->roleid,
            $this->email,
            ) = $data;
    }
}
