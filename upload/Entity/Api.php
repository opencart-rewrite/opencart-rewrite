<?php
namespace Entity;
/**
 * Represent a api user of opencart
 *
 * @Entity(
 *     repositoryClass="Entity\ApiRepository"
 * )
 * @Table(name="oc_api")
 *
 * @category Entity
 * @package  Opencart
 * @author   Allan SIMON <allan.simon@supinfo.com>
 */
class Api
{
    /**
     *
     */
    public function __construct()
    {
        // equivalent to set the value to NOW() in SQL
        $this->dateAdded = new \DateTime();
        $this->dateModified = new \DateTime();
    }

    /**
     * @Id
     * @Column(
     *     name="api_id",
     *     type="integer"
     * )
     * @GeneratedValue
     *
     */
    protected $id;

    /** @Column(type="string") **/
    protected $password;

    /**
     * @Column(
     *     name="date_added",
     *     type="datetime"
     * )
     */
    protected $dateAdded;

    /**
     * @Column(
     *     name="date_modified",
     *     type="datetime"
     * )
     */
    protected $dateModified;

    /** @Column(type="string") **/
    protected $username;

    /** @Column(type="boolean") **/
    protected $status = true;

    public function getId()
    {
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

    public function isActivated()
    {
        return $this->status;
    }

    public function setStatus($status)
    {
        $this->status = $status;
    }

    public function getPassword()
    {
        return $this->password;
    }

    public function setPassword($password)
    {
        $this->password = $password;
    }

    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    public function getDateModified()
    {
        return $this->dateModified;
    }

    public function setDateModified(\DateTime $dateModified)
    {
        $this->dateModified = $dateModified;
        return $this;
    }
}
