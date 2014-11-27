<?php
/**
 * Entity Category.
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
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Entity Category.
 *
 * Represent the basic structure of a category
 * the one which is independant of the language
 * for language dependant data, see CategoryDescription
 *
 * @Entity(
 *     repositoryClass="Entity\CategoryRepository"
 * )
 * @Table(name="oc_category")
 *
 * @category Entity
 * @package  Opencart
 * @author   Pierre GUILLEMOT <pierreguilemot@yahoo.fr>
 * @license  http:/www.gnu.org/copyleft/gpl.html GPLv3
 * @link     http: //github.com/opencart-rewrite
 */
class Category
{
    /**
     *
     */
    public function __construct()
    {
        // equivalent to set the value to NOW() in SQL
        $this->dateAdded = new \DateTime();
        $this->dateModified = new \DateTime();
        $this->descriptions = new ArrayCollection();
    }

    /**
     * @Id
     * @Column(
     *      name="category_id",
     *      type="integer"
     * )
     * @GeneratedValue
     *
     */
    protected $id;

    /** @Column(type="string") **/
    protected $image = '';

    /** @Column(name="parent_id", type="integer") **/
    protected $parentId = 0;

    /** @Column(type="boolean") **/
    protected $top = false;

    /** @Column(name="`column`", type="integer") **/
    protected $column = 0;

    /** @Column(name="sort_order", type="integer") **/
    protected $sortOrder = 0;

    /** @Column(type="boolean") **/
    protected $status = true;
    /**
     * @Column(
     *     name="date_added",
     *     type="datetime"
     * )
     *
     */
    protected $dateAdded;

    /**
     * @Column(
     *     name="date_modified",
     *     type="datetime"
     * )
     *
     */
    protected $dateModified;

    /**
     * @ORM\OneToMany(targetEntity="Product", mappedBy="status")
     *
     */
    protected $descriptions;

    protected $children = array();


    protected $parent;
    /**
     *
     */
    public function addDescription(CategoryDescription $description)
    {
        $this->descriptions->add($description);
    }

    /**
     *
     */
    public function getId()
    {
        return $this->id;
    }

    public function getDateAdded()
    {
        return $this->dateAdded;
    }

    /**
     *
     */
    public function getDateModified()
    {
        return $this->dateModified;
    }

    /**
     *
     */
    public function setDateModified(\DateTime $dateModified)
    {
        $this->dateModified = new \DateTime();
        return $this;
    }

    
    /**
     * Set image.
     *
     * @param image the value to set.
     * @return Category
     */
    public function setImage($image)
    {
        $this->image = $image;
        return $this;
    }
    /**
     *
     */
    public function getParentId()
    {
        return $this->parentId;
    }
    
    /**
     * Set parentId.
     *
     * @param parentId the value to set.
     * @return Category
     */
    public function setParentId($parentId)
    {
        $this->parentId = $parentId;
        return $this;
    }
    
    /**
     * Set top.
     *
     * @param top the value to set.
     * @return Category
     */
    public function setTop($top)
    {
        $this->top = $top;
        return $this;
    }
    
    /**
     * Set column.
     *
     * @param column the value to set.
     * @return Category
     */
    public function setColumn($column)
    {
        $this->column = $column;
        return $this;
    }
    
    /**
     * Set sortOrder.
     *
     * @param sortOrder the value to set.
     * @return Category
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = $sortOrder;
        return $this;
    }
    
    /**
     * Set status.
     *
     * @param status the value to set.
     * @return Category
     */
    public function setStatus($status)
    {
        $this->status = $status;
        return $this;
    }
}
