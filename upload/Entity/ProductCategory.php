<?php
/**
 * Entity Product Category.
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
 * Entity ProductCategory.
 *
 * Represent the join table between Product and Category
 * TODO: once we got the entity "Product", this entity can be removed
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
class ProductCategory
{

    /**
     * @Id
     * @Column(name="category_id", type="integer")
     */
    protected $categoryId;

    /**
     * @Id
     * @Column(name="product_id", type="integer")
     */
    protected $productId;


    
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
     * @return ProductCategory
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }
    
    
    /**
     * Get productId.
     *
     * @return integer $productId.
     */
    public function getProductId()
    {
        return $this->productId;
    }
    
    /**
     * Set productId.
     *
     * @param integer $productId the value to set.
     *
     * @return ProductCategory
     */
    public function setProductId($productId)
    {
        $this->productId = $productId;
        return $this;
    }
}
