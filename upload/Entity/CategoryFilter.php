<?php
/**
 * Entity Category Filter.
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
 * Entity CategoryFilter.
 *
 * Represent the join table between Category and Filter
 * TODO: once we got the entity "Filter", this entity can be removed
 *
 * @Entity
 * @Table(name="oc_category_filter")
 *
 * @category Entity
 * @package  Opencart
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  http:/www.gnu.org/copyleft/gpl.html GPLv3
 * @link     http: //github.com/opencart-rewrite
 */
class CategoryFilter
{

    /**
     * @Id
     * @Column(name="category_id", type="integer")
     */
    protected $categoryId;

    /**
     * @Id
     * @Column(name="filter_id", type="integer")
     */
    protected $filterId;


    
    /**
     * Get categoryId.
     *
     * @return int $categoryId.
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }
    
    /**
     * Set categoryId.
     *
     * @param int $categoryId the value to set.
     *
     * @return CategoryFilter
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }
    
    /**
     * Get filterId.
     *
     * @return int $filterId.
     */
    public function getFilterId()
    {
        return $this->filterId;
    }
    
    /**
     * Set filterId.
     *
     * @param int $filterId the value to set.
     *
     * @return CategoryFilter
     */
    public function setFilterId($filterId)
    {
        $this->filterId = $filterId;
        return $this;
    }
}
