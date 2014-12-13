<?php
/**
 * Entity_Customer.
 *
 * PHP version 5
 *
 * @category Entity
 * @package  Opencart
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  http:/www.gnu.org/copyleft/gpl.html GPLv3
 * @link     http: //github.com/opencart-rewrite
 */
namespace Entity;
/**
 * Represent the opencart's setting
 * TODO: currently opencart has a mix of seralized and not serialized
 * value
 *
 * @Entity(
 *     repositoryClass="Entity\SettingRepository"
 * )
 * @Table(name="oc_setting")
 *
 * @category Entity
 * @package  Opencart
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  http:/www.gnu.org/copyleft/gpl.html GPLv3
 * @link     http: //github.com/opencart-rewrite
 */
class Setting
{
    /**
     * @Id
     * @Column(
     *     name="setting_id",
     *     type="integer"
     * )
     * @GeneratedValue
     *
     */
    protected $id;

    /**
     * @Column(
     *     name="store_id",
     *     type="integer"
     * )
     *
     */
    protected $storeId;

    /** @Column(type="string") **/
    protected $code;

    /** @Column(type="string") **/
    protected $key;

    /**
     * TODO: actually sometimes it is type "array" (i.e serialized php)
     * @Column(type="string")
     */
    protected $value;

    /** @Column(type="boolean") **/
    protected $serialized = false;

    public function getId()
    {
        return $this->id;
    }

    public function setCode($code)
    {
        $this->code = $code;
        return $this;
    }

    public function getCode()
    {
        return $this->code;
    }


    public function setStoreId($storeId)
    {
        $this->storedId = $storeId;
        return $this;
    }

    public function getStoreId()
    {
        return $this->storeId;
    }

    public function setKey($key)
    {
        $this->key = $key;
        return $this;
    }

    public function getKey()
    {
        return $this->key;
    }

    public function setValue($value)
    {
        if (is_array($value)) {
            $this->serialized = true;
            $value = serialize($value);
        }

        $this->value = $value;

        return $this;
    }

    public function getValue()
    {
        if ($this->seralized) {
            return unserialize($this->value);
        }
        return $this->value;
    }
}
