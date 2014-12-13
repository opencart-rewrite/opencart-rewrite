<?php
/**
 * Entity Category Layout.
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
 * Entity CategoryLayout.
 *
 * Represent the join table betwee Category and Store and layout
 * we have to represent it as a Entity because in opencart
 * we store "no store/default store" as store_id = 0
 * which is not linked to anything...
 *
 * @Entity
 * @Table(name="oc_category_to_layout")
 *
 * @category Entity
 * @package  Opencart
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  http:/www.gnu.org/copyleft/gpl.html GPLv3
 * @link     http: //github.com/opencart-rewrite
 */
class CategoryLayout
{

    /**
     * @Id
     * @Column(name="category_id", type="integer")
     */
    protected $categoryId;

    /**
     * @Id
     * @Column(name="store_id", type="integer")
     */
    protected $storeId;

   /**
     * @Id
     * @Column(name="layout_id", type="integer")
     */
    protected $layoutId;
  
    
    /**
     * Get categoryId.
     *
     * @return categoryId.
     */
    public function getCategoryId()
    {
        return $this->categoryId;
    }
    
    /**
     * Set categoryId.
     *
     * @param integer $categoryId the value to set.
     *
     * @return CategoryStore
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }
    
    /**
     * Get storeId.
     *
     * @return storeId.
     */
    public function getStoreId()
    {
        return $this->storeId;
    }
    
    /**
     * Set storeId.
     *
     * @param integer $storeId the value to set.
     *
     * @return CategoryStore
     */
    public function setStoreId($storeId)
    {
        $this->storeId = $storeId;
        return $this;
    }
    
    /**
     * Get layoutId.
     *
     * @return integer $layoutId.
     */
    public function getLayoutId()
    {
        return $this->layoutId;
    }
    
    /**
     * Set layoutId.
     *
     * @param integer $layoutId the value to set.
     *
     * @return CategoryLayout
     */
    public function setLayoutId($layoutId)
    {
        $this->layoutId = $layoutId;
        return $this;
    }
}
