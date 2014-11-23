<?php
namespace Entity;
/**
 * Represent a user of opencart
 *
 * @Entity
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
    
    public function setUsername(string $username)
    {
        $this->username = $username;
    }

    public function setFirstname(string $firstname)
    {
        $this->firstname = $firstname;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setLastname(string $lastname)
    {
        $this->lastname = $lastname;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setEmail(string $email)
    {
        $this->email = $email;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setGroupId(int $groupId)
    {
        $this->groupId = $groupId;
    }

    public function getGroupId()
    {
        return $this->groupId;
    }

    public function setImage(string $image)
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

    public function setStatus(boolean $status)
    {
        $this->status = $status;
    }

    public function setCode(string $code)
    {
        $this->code = $code;
    }


    public function setPassword(string $password)
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