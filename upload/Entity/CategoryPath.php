<?php
/**
 * Entity Category Path
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
 * Entity Category Path
 *
 * Represent the node of a path from a leaf category
 * to its route
 * with a "level" to know where this node is positioned
 * on the path
 *
 * @Entity
 * @Table(name="oc_category_path")
 *
 * @category Entity
 * @package  Opencart
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  http:/www.gnu.org/copyleft/gpl.html GPLv3
 * @link     http: //github.com/opencart-rewrite
 */
class CategoryPath
{
    /**
     * terminal leaf of this path
     * @var integer
     * @Id
     * @Column(name="category_id", type="integer")
     */
    protected $categoryId;

    /**
     * category at this level of the path
     * @var integer
     * 
     * @Id
     * @Column(name="path_id", type="integer")
     */
    protected $pathId;

    /**
     * represent how far we are from the root of the path
     * 0 = this is the root
     * @var integer
     * @Column(type="integer")
     */
    protected $level;

    
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
     * @return CategoryPath
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = $categoryId;
        return $this;
    }
    
    /**
     * Get pathId.
     *
     * @return pathId.
     */
    public function getPathId()
    {
        return $this->pathId;
    }
    
    /**
     * Set pathId.
     *
     * @param integer $pathId the value to set.
     *
     * @return CategoryPath
     */
    public function setPathId($pathId)
    {
        $this->pathId = $pathId;
        return $this;
    }
    
    /**
     * Get level.
     *
     * @return level.
     */
    public function getLevel()
    {
        return $this->level;
    }
    
    /**
     * Set level.
     *
     * @param level the value to set.
     *
     * @return CategoryPath
     */
    public function setLevel($level)
    {
        $this->level = $level;
        return $this;
    }
}
