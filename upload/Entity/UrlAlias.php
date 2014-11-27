<?php
/**
 * Entity UrlAlias
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
 * Entity Url Alias.
 *
 * Permits to do some URL tweaking by mapping parameter=value
 * to more SEO friendly strings
 *
 * @Entity
 * @Table(name="oc_url_alias")
 *
 * @category Entity
 * @package  Opencart
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  http:/www.gnu.org/copyleft/gpl.html GPLv3
 * @link     http: //github.com/opencart-rewrite
 */
class UrlAlias
{
    
    /**
     * @Id
     * @Column(
     *     name="url_alias_id",
     *     type="integer"
     * )
     * @GeneratedValue
     *
     */
    protected $id;

    /** @Column(type="string") **/
    protected $query;

    /** @Column(type="string") **/
    protected $keyword;

    /**
     * Set keyword.
     *
     * @param string $keyword the value to set.
     *
     * @return UrlAlias
     */
    public function setKeyword($keyword)
    {
        $this->keyword = $keyword;
        return $this;
    }
    
    /**
     * Get keyword.
     *
     * @return string
     */
    public function getKeyword()
    {
        return $this->keyword;
    }
    
    /**
     * Set query.
     *
     * @param mixed $query the value to set.
     *
     * @return UrlAlias
     */
    public function setQuery($query)
    {
        $this->query = $query;
        return $this;
    }
    
    /**
     * Get query.
     *
     * @return string
     */
    public function getQuery()
    {
        return $this->query;
    }
    
    /**
     * Get id.
     *
     * @return integer $id.
     */
    public function getId()
    {
        return $this->id;
    }
}
