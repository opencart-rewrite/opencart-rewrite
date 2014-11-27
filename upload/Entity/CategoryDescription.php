<?php
/**
 * Entity Category Description
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
 * Entity Category Description.
 *
 * Represent only the part of a category which is language
 * dependant, for the hierarchy etc. see entity Category
 *
 * @Entity
 * @Table(name="oc_category_description")
 *
 * @category Entity
 * @package  Opencart
 * @author   Allan SIMON <allan.simon@supinfo.com>
 * @license  http:/www.gnu.org/copyleft/gpl.html GPLv3
 * @link     http: //github.com/opencart-rewrite
 */
class CategoryDescription
{
    /**
     * @Id
     * @Column(name="category_id", type="integer")
     */
    protected $categoryId;

    /**
     * @Id
     * @Column(name="language_id", type="integer")
     */
    protected $languageId;

    /** @Column(type="string") **/
    protected $name;

    /** @Column(type="string") **/
    protected $description;

    /** @Column(name="meta_description", type="string") **/
    protected $metaDescription;

    /** @Column(name="meta_title", type="string") **/
    protected $metaTitle;

    /** @Column(name="meta_keyword", type="string") **/
    protected $metaKeyword;

    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Category", inversedBy="descriptions")
     * @ORM\JoinColumn(name="category_id", referencedColumnName="id")
     */
    protected $category;

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
     *
     */
    public function setCategory(Category $category)
    {
        $this->categoryId = $category->getId();
        $this->category = $category;
        return $this;
    }

    /**
     * Get languageId.
     *
     * @return languageId.
     */
    public function getLanguageId()
    {
        return $this->languageId;
    }
    
    /**
     * Set languageId.
     *
     * @param $languageId the value to set.
     */
    public function setLanguageId($languageId)
    {
        $this->languageId = $languageId;
        return $this;
    }
    
    /**
     * Get name.
     *
     * @return name.
     */
    public function getName()
    {
        return $this->name;
    }
    
    /**
     * Set name.
     *
     * @param $name the value to set.
     */
    public function setName($name)
    {
        $this->name = $name;
        return $this;
    }
    
    /**
     * Get description.
     *
     * @return description.
     */
    public function getDescription()
    {
        return $this->description;
    }
    
    /**
     * Set description.
     *
     * @param $description the value to set.
     */
    public function setDescription($description)
    {
        $this->description = $description;
        return $this;
    }
    
    /**
     * Get metaDescription.
     *
     * @return string
     */
    public function getMetaDescription()
    {
        return $this->metaDescription;
    }
    
    /**
     * Set metaDescription.
     *
     * @param string $metaDescription the value to set.
     *
     * @return void
     */
    public function setMetaDescription($metaDescription)
    {
        $this->metaDescription = $metaDescription;
        return $this;
    }
    
    /**
     * Get metaTitle.
     *
     * @return string
     */
    public function getMetaTitle()
    {
        return $this->metaTitle;
    }
    
    /**
     * Set metaTitle.
     *
     * @param string $metaTitle the value to set.
     */
    public function setMetaTitle($metaTitle)
    {
        $this->metaTitle = $metaTitle;
        return $this;
    }
    
    /**
     * Get metaKeyword.
     *
     * @return string metaKeyword.
     */
    public function getMetaKeyword()
    {
        return $this->metaKeyword;
    }
    
    /**
     * Set metaKeyword.
     *
     * @param string $metaKeyword the value to set.
     *
     * @return CategoryDescription
     */
    public function setMetaKeyword($metaKeyword)
    {
        $this->metaKeyword = $metaKeyword;
        return $this;
    }
}
