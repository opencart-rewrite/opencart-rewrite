<?php
/**
 * Entity_Customer.
 *
 * PHP version 5
 *
 * @category Entity
 * @package  Opencart
 * @author   Pierre GUILLEMOT <pierreguilemot@yahoo.fr>
 * @license  http:/www.gnu.org/copyleft/gpl.html GPLv3
 * @version  GIT: $Id: Customer.php,v 0.1 2014/11/22 entity Exp $
 * @link     http: //github.com/opencart-rewrite
 */

namespace Entity;

/**
 * Entity_Customer.
 *
 * Represent a customer of opencart
 *
 * @Entity
 * @Table(name="oc_customer")
 *
 * @category Entity
 * @package  Opencart
 * @author   Pierre GUILLEMOT <pierreguilemot@yahoo.fr>
 * @license  http:/www.gnu.org/copyleft/gpl.html GPLv3
 * @link     http: //github.com/opencart-rewrite
 */
class Customer
{

    /**
     * TODO
     */
    public function __construct()
    {
        //Same construction as the admin user
        $this->dateAdded = new \DateTime();
        $this->salt = substr(md5(uniqid(rand(), true)), 0, 9);
    }

    /**
     * @Id
     * @Column(
     *      name="customer_id",
     *      type="integer"
     * )
     * @GeneratedValue
     *
     */
    protected $id;

    /**
     * @Column(
     *      name="customer_group_id",
     *      type="integer"
     * )
     *
     */
    protected $customerGroupId;

    /**
     * @Column(
     *      name="store_id",
     *      type="integer"
     * )
     *
     */
    protected $storeId;

    /**
     * @Column(
     *      name="address_id",
     *      type="integer"
     * )
     *
     */
    protected $addressId;

    /** @Column(type="string") **/
    protected $firstname;

    /** @Column(type="string") **/
    protected $lastname;

    /** @Column(type="string") **/
    protected $email;

    /** @Column(type="string") **/
    protected $telephone;

    /** @Column(type="string") **/
    protected $fax;

    /** @Column(type="string") **/
    protected $password;

    /** @Column(type="string") **/
    protected $salt;

    /** @Column(type="string") **/
    protected $cart;

    /** @Column(type="string") **/
    protected $wishlist;

    /** @Column(type="boolean") **/
    protected $newsletter;

    /** @Column(type="string") **/
    protected $custom_field;

    /** @Column(type="string") **/
    protected $ip;

    /** @Column(type="boolean") **/
    protected $status;

    /** @Column(type="boolean") **/
    protected $approved;

    /** @Column(type="boolean") **/
    protected $safe;

    /** @Column(type="string") **/
    protected $token;

    /**
     * @Column(
     *     name="date_added",
     *     type="datetime"
     * )
     *
     */
    protected $dateAdded;

    /**
     * Returns whether the customer is approved or no
     *
     * @return boolean approved
     */
    public function isApproved()
    {
        return $this->approved;
    }
}
