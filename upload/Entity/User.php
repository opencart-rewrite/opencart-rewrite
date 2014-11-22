<?php
namespace Entity;
/**
 * Represent a user of opencart
 *
 * @Entity(
 *     repositoryClass="Entity\UserRepository"
 * )
 * @Table(name="oc_user")
 *
 * @category Entity
 * @package  Opencart
 * @author   Allan SIMON <allan.simon@supinfo.com>
 */
class User
{

    /**
     *
     */
    public function __construct()
    {
        // equivalent to set the value to NOW() in SQL
        $this->dateAdded = new \DateTime();
        $this->salt = substr(md5(uniqid(rand(), true)), 0, 9);
    }

    /**
     * @Id
     * @Column(
     *     name="user_id",
     *     type="integer"
     * )
     * @GeneratedValue
     *
     */
    protected $id;

    /**
     * @Column(
     *     name="user_group_id",
     *     type="integer"
     * )
     *
     */
    protected $groupId;

    /** @Column(type="string") **/
    protected $username;

    /** @Column(type="string") **/
    protected $lastname;

    /** @Column(type="string") **/
    protected $firstname;

    /** @Column(type="string") **/
    protected $email;

    /** @Column(type="string") **/
    protected $salt;

    /** @Column(type="string") **/
    protected $image = '';

    /** @Column(type="string") **/
    protected $password;

    /** @Column(type="string") **/
    protected $ip = '';

    /**
     * Reset password code
     * @var string
     *
     * @Column(type="string")
     */
    protected $code = '';

    /**
     * @Column(
     *     name="date_added",
     *     type="datetime"
     * )
     */
    protected $dateAdded;

    /** @Column(type="boolean") **/
    protected $status = true;

    public function getId() {
        return $this->id;
    }

    public function getUsername()
    {
        return $this->username;
    }
    
    public function setUsername($username)
    {
        $this->username = $username;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setEmail($email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setIp($ip)
    {
        return $this->ip;
    }

    public function setGroupId($groupId)
    {
        $this->groupId = $groupId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function setImage($image)
    {
        $this->image = $image;
    }

    public function getImage()
    {
        return $this->image;
    }

    public function isActivated()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function setCode($code)
    {
        $this->code = $code;
    }

    public function getSalt()
    {
        return $this->salt;
    }

    public function getHashedPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $hashedPassword = sha1(
            $this->salt . sha1(
                $this->salt . sha1($password)
            )
        );
        $this->password = $hashedPassword;
    }

    public function getDateAdded()
    {
        return $this->dateAdded;
    }
}
