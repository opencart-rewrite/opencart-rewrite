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
     * @Id
     * @Column(
     *     name="user_id",
     *     type="integer"
     * )
     * @GeneratedValue
     *
     */
    protected $id;
    /** @Column(type="string") **/
    protected $username;

    /** @Column(type="string") **/
    protected $lastname;

    /** @Column(type="string") **/
    protected $firstname;

    /** @Column(type="string") **/
    protected $email;

    /** @Column(type="boolean") **/
    protected $status;
}
