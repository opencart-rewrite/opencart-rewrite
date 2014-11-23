<?php
namespace Entity;
/**
 * Represent a user of opencart
 *
 * @Entity(
 *     repositoryClass="Entity\UserGroupRepository"
 * )
 * @Table(name="oc_user_group")
 *
 * @category Entity
 * @package  Opencart
 * @author   Allan SIMON <allan.simon@supinfo.com>
 */
class UserGroup
{

    /**
     * @Id
     * @Column(
     *     name="user_group_id",
     *     type="integer"
     * )
     * @GeneratedValue
     *
     */
    protected $id;

    /** @Column(type="string") **/
    protected $name;

    /**
     * Note: we ue array and not json array to keep compatibility
     * with existing database
     * @Column(type="array")
     *
     */
    protected $permission;


    /**
     *
     */
    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     *
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * TODO: rename in 'setPermissions'
     */
    public function setPermission($permission)
    {
        $this->permission = $permission;
    }

    /**
     *
     */ 
    public function addPermission($type, $route)
    {
        $this->permission[$type][] = $route;
    }
}
