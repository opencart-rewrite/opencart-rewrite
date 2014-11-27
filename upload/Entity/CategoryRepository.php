<?php
/**
 * Repository Category
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
use Doctrine\ORM\EntityRepository;



/**
 *
 */
class CategoryRepository extends EntityRepository
{

    /**
     * event object from opencart
     */
    protected $event;

    /**
     * cache object from opencart
     */
    protected $cache;

    /**
     *
     */
    public function setEventManager($event)
    {
        $this->event = $event;
    }

    /**
     *
     */
    public function setCacheManager($cache)
    {
        $this->cache = $cache;
    }

    /**
     *
     */
    public function add($data)
    {
        $this->event->trigger('pre.admin.category.add', $data);
        $em = $this->getEntityManager();

        $category = new \Entity\Category();

        //try /catch to put everything in a big transaction
        try {
            $connection = $em->getConnection();
            $connection->beginTransaction();

            $this->_insertCategoryAndRelated($data, $category);

            // MySQL Hierarchical Data Closure Table Pattern
            $this->_createCategoryPath($category);

            $connection->commit();

        } catch (Exception $e) {
            $connection->rollback();
            throw $e;
        }

        $this->cache->delete('category');
        $this->event->trigger(
            'post.admin.category.add',
            $category->getId()
        );

        return $category->getId();
    }
    
    /**
     *
     */
    public function edit($categoryId, $data)
    {
        $this->event->trigger('pre.admin.category.edit', $data);
        $em = $this->getEntityManager();

        //try /catch to put everything in a big transaction
        $connection = $em->getConnection();
        try {
            $connection->beginTransaction();

            $category = $this->find($categoryId);
            $oldParentId = $category->getParentId();

            $this->_deleteRelatedToCategory($categoryId);

            $category = $this->_insertCategoryAndRelated($data, $category);

            // no need to update the Category hierarchy
            // if we haven't changed the parent of this category
            if ($oldParentId !== (int) $data['parent_id']) {
                $this->_updateCategoryPath($category);
            }
            $connection->commit();

        } catch (Exception $e) {
            $connection->rollback();
            throw $e;
        }

        $this->cache->delete('category');
        $this->event->trigger(
            'post.admin.category.edit',
            $categoryId
        );

    }

    /**
     *
     */
    public function delete($categoryId)
    {
        $this->event->trigger('pre.admin.category.delete', $categoryId);
        $em = $this->getEntityManager();

        $this->_deleteOwnPath($categoryId);
        // delete recursively all categories that
        // are children/grandchildren etc. of current category

        $dql = '
            SELECT DISTINCT p.categoryId
            FROM \Entity\CategoryPath p
            WHERE p.pathId = :pathId
        ';
        $categoryIds = $em->createQuery($dql)
            ->setParameter('pathId', $categoryId)
            ->getScalarResult()
        ;
        foreach ($categoryIds as $oneCategoryId) {
            $this->delete($oneCategoryId['categoryId']);
        }
        
        $this->_deleteRelatedToCategory($categoryId);
        $this->_deleteOfCategory("Entity\ProductCategory", $categoryId);

        $this->cache->delete('category');

        $this->event->trigger('post.admin.category.delete', $category_id);
    }

    /**
     * Get description in all languages for the given category
     */
    public function getDescriptions($categoryId)
    {
        $descriptions = $this->createQueryBuilder('Entity\CategoryDescription')
            ->select('d')
            ->distinct()
            ->from('Entity\CategoryDescription', 'd')
            ->where('d.categoryId = :categoryId')
            ->setParameter('categoryId', (int) $categoryId)
            ->getQuery()
            ->getArrayResult();
        ;

        $descriptionsData = array();
        foreach ($descriptions as $result) {

            $languageId = $result['languageId'];
            $descriptionsData[$languageId] = array(
                'name' => $result['name'],
                'meta_title' => $result['metaTitle'],
                'meta_description' => $result['metaDescription'],
                'meta_keyword' => $result['metaKeyword'],
                'description' => $result['description']
            );
        }

        return $descriptionsData;
    }

    /**
     *
     */
    public function getFilterIds($categoryId)
    {
        $filterIds = $this->createQueryBuilder('Entity\CategoryFilter')
            ->select('f.filterId')
            ->distinct()
            ->from('Entity\CategoryFilter', 'f')
            ->where('f.categoryId = :categoryId')
            ->setParameter('categoryId', (int) $categoryId)
            ->getQuery()
            ->getScalarResult();
        ;

        return $filterIds;
    }

    /**
     *
     */
    public function getStoreId($categoryId)
    {
        $stores = $this->createQueryBuilder('Entity\CategoryStore')
            ->select('s.storeId')
            ->distinct()
            ->from('Entity\CategoryStore', 's')
            ->where('s.categoryId = :categoryId')
            ->setParameter('categoryId', (int) $categoryId)
            ->getQuery()
            ->getScalarResult();
        ;

        return $stores;
    }

    /**
     *
     */
    public function count()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT COUNT(c.id) FROM Entity\Category c')
            ->getSingleScalarResult();
    }

    /**
     *
     */
    public function countByLayoutId($layoutId)
    {
        $dql = '
            SELECT COUNT(l.id)
            FROM Entity\CategoryLayout l
            WHERE l.layoutId = :layoutId
        ';

        return $this->getEntityManager()
            ->createQuery($dql)
            ->setParameter('layoutId', $layoutId)
            ->getSingleScalarResult()
        ;
    }


    /**
     *
     */
    public function getPathString(
        $categoryId,
        $languageId = 1,
        $separator = ' > '
    ) {
        //Note: original opencart code was using a 
        // GROUP_CONCAT, but it's a "MySQL"-only function

        $em = $this->getEntityManager();
        $dql = '
            SELECT DISTINCT d.name
            FROM \Entity\CategoryDescription d
            JOIN \Entity\CategoryPath p
            WITH d.categoryId = p.pathId
            WHERE
                d.languageId = :languageId AND
                p.categoryId = :categoryId
            ORDER BY p.level
        ';
        $nameResults = $em->createQuery($dql)
            ->setParameter('languageId', (int) $languageId)
            ->setParameter('categoryId', (int) $categoryId)
            ->getScalarResult()
        ;
        $names = array_map('current', $nameResults);

        return implode($separator, $names);
    }

    /**
     * TODO move in URL alias repository
     */
    public function getKeyword($categoryId)
    {
        $em = $this->getEntityManager();

        // MIN() is to force a line containing NULL if no result
        // (otherwise if SELECT returns nothing, getSingleScalarResult
        // will throw an exception
        $dql = '
            SELECT MIN(u.keyword)
            FROM Entity\UrlAlias u
            WHERE u.query = :query
        ';
            
        $keyword = $em->createQuery($dql)
            ->setParameter('query', "category_id=" . $categoryId)
            ->getSingleScalarResult()
        ;
        return $keyword;
    }

    /**
     * TODO move in category layout repository
     */
    public function getCategoryLayoutsArray($categoryId)
    {
        $layoutRepo = $this->getEntityManager()->getRepository(
            'Entity\CategoryLayout'
        );
        $layouts = $layoutRepo->findByCategoryId($categoryId);
        
        $results = array();
        foreach ($layouts as $oneLayout) {
            $results[$oneLayout->getStoreId()] = $oneLayout->getLayoutId();
        }
        return $results;
    }

    /**
     *
     */
    public function getCategory($categoryId)
    {
        $categoryId = (int) $categoryId;
        // get category

        $em = $this->getEntityManager();
        $dql = "
            SELECT c FROM Entity\Category c WHERE c.id = :categoryId";
        $categoryArray = $em->createQuery($dql)
            ->setParameter('categoryId', $categoryId)
            ->getArrayResult()
        ;
        return empty($categoryArray) ?
            null :
            $categoryArray[0]
        ;
    }

    /**
     *
     */
    public function getAutocompletionCategories(
        $filter = ''
    ) {

        // emulate get all + GROUP_CONCAT 
        $categories = $this->_findAllWithPath();
        // emulate the order by result of group concat
        $categories = $this->_orderBy($categories, 'name', 'ASC');

        if ($filter !== '') {
            $categories = array_filter(
                $categories,
                function ($category) use ($filter) {
                    $name = $category['name'];
                    return strpos($name, $filter) !== false;
                }
            ); 
        }
        // emulate limit / offset
        $categories = array_slice($categories, 0, 5);
        return $categories;

    }

    /**
     *
     */
    public function getCategoriesPaginated(
        $start = 0,
        $limit = 20,
        $orderBy = 'sortOrder',
        $order = 'ASC'
    ) {
        // we emulate in php the GROUP_CONCAT etc. SQL request
        // the advantage is that by doing so we don't need to use
        // a Mysql-only function
        // the disvantage being that we need to retrieve all
        // the values first then doing a foreach of SQL request
        // TODO we can mitigate this by using cache

        // emulate get all + GROUP_CONCAT 
        $categories = $this->_findAllWithPath();

        // emulate the order by result of group concat

        $categories = $this->_orderBy($categories, $orderBy, $order);
        // emulate limit / offset
        $categories = array_slice($categories, $start, $limit);
        return $categories;
    }

    /**
     *
     */
    private function _createCategoryPath($category)
    {
        $em = $this->getEntityManager();

        $level = 0;
        $categoryId = $category->getId();
        $parentId = $category->getParentId();

        // the idea is the following, the tree is flatten
        // into a list of path leaft to root
        // category_id is the "terminal leaf" and all the row
        // with the same category_id belongs to the same path, 
        // the row with level 0 represent the root

        // get all the node of the parent path to root
        
        //TODO use relationship to retrieve the path nodes
        $pathNodes = $em->getRepository('Entity\CategoryPath')->findBy(
            array('categoryId'=> $parentId)
        );
        $level = count($pathNodes);

        // and we we will use to create the path for this new leaf
        // i.e  (if parent_id is 58, and  new category id is 100) 
        //      this will be used to create  ..........      that
        // ________________________________    ________________________________
        // | category_id | path_id | level |  | category_id | path_id | level |
        // +-------------+---------+-------+  +-------------+---------+-------+
        // |          58 |      34 |     0 |->|         100 |      34 |     0 |
        // |          58 |      52 |     1 |  |         100 |      52 |     1 |
        // |          58 |      58 |     2 |  |         100 |      58 |     2 |
        // |             |         |       |  |         100 |     100 |     3 |

        foreach ($pathNodes as $node) {
            $newNode = new CategoryPath();
            $newNode->setCategoryId($categoryId)
                ->setPathId($node->getPathId())
                ->setLevel($node->getLevel())
            ;
            $em->persist($newNode);
        }
        $leafNode = new CategoryPath();
        $leafNode->setCategoryId($categoryId)
            ->setPathId($categoryId)
            ->setLevel($level)
        ;
        $em->persist($leafNode);
        $em->flush();
    }

    /**
     *
     */
    private function _updateCategoryPath($category)
    {
        $categoryId = $category->getId();
        $em = $this->getEntityManager();
        // get all pathNode containing this category as a node
        // (so we retrieve even the path for which the category
        // is terminal leaf
        $dql = '
            SELECT DISTINCT c
            FROM \Entity\Category c
            JOIN \Entity\CategoryPath p
            WITH c.id = p.categoryId
            WHERE p.pathId = :pathId
        ';

        $categories = $em->createQuery($dql)
            ->setParameter('pathId', $categoryId)
            ->getResult()
        ;
        // delete all path from children and ourselves
        // and recreate them
        // TODO this part could be optimized in term of queries
        // to do less delete/select/insert
        // i.e we should be to leverage the fact that
        //   1. for all these categories the part to the root is common
        //      between them
        //   2. the part which is not common only need to have their "level"
        //      incremented by the size of the common root part
        // after, as changing the parent of a category is not common operation
        // and normally hierarchy are not very complex, it may be better
        // to keep current code as it's simpler to explain
        foreach ($categories as $childCategory) {
            $this->_deleteOwnPath($childCategory->getId());
            $this->_createCategoryPath($childCategory);
        }

    }

    /**
     * Delete path for which the give category Id is the terminal
     * leaf
     */
    private function _deleteOwnPath($categoryId)
    {
        var_dump($categoryId);
        echo "prout";
        $em = $this->getEntityManager();
        $dql = "
            DELETE FROM Entity\CategoryPath p
            WHERE p.categoryId = :categoryId
        ";
        $em->createQuery($dql)
            ->setParameter('categoryId', $categoryId)
            ->execute()
        ;

        echo "pouet";
    }

    /**
     *
     */
    private function _saveDescriptions($descriptionsData, $category)
    {
        $em = $this->getEntityManager();

        foreach ($descriptionsData as $languageId => $value) {
            $description = new CategoryDescription();
            $description->setLanguageId($languageId)
                ->setCategory($category)
                ->setName($value['name'])
                ->setDescription($value['description'])
                ->setMetaTitle($value['meta_title'])
                ->setMetaDescription($value['meta_description'])
                ->setMetaKeyword($value['meta_keyword'])
            ;
            $em->persist($description);
            $em->flush();
        }
    }

    /**
     *
     */
    private function _savesFilters(
        array $filtersData,
        Category $category
    ) {
        $em = $this->getEntityManager();

        foreach ($filtersData as $filterId) {
            $categoryFilter = new CategoryStore();
            $categoryFilter->setCategoryId($category->getId())
                ->setFilterId((int) $filterId)
            ;
            $em->persist($filterId);
        }
        $em->flush();
    }



    /**
     *
     */
    private function _savesStores(
        array $storesData,
        Category $category
    ) {
        $em = $this->getEntityManager();

        foreach ($storesData as $storeId) {
            $categoryStore = new CategoryStore();
            $categoryStore->setCategoryId($category->getId())
                ->setStoreId((int) $storeId)
            ;
            $em->persist($categoryStore);
        }
        $em->flush();
    }

    /**
     *
     */
    private function _savesLayouts(
        array $layoutData,
        Category $category
    ) {
        $em = $this->getEntityManager();

        foreach ($layoutData as $storeId => $layoutId) {
            $categoryStore = new CategoryLayout();
            $categoryStore->setCategoryId($category->getId())
                ->setStoreId((int) $storeId)
                ->setLayoutId((int) $layoutId)
            ;
            $em->persist($categoryStore);
        }
        $em->flush();
    }

    /**
     *
     */
    private function _updateCategory(Category $category, $data)
    {
        $em = $this->getEntityManager();

        $category->setParentId((int) $data['parent_id'])
            ->setTop((int) $data->get('top', 0))
            ->setColumn((int) $data['column'])
            ->setSortOrder((int) $data['sort_order'])
            ->setStatus((int) $data['status'])
            ->setImage($data['image'])
        ;

        $em->persist($category);
        $em->flush();

        return $category;
    }

    /**
     *
     */
    private function _saveKeyword($keyword, Category $category)
    {
        $em = $this->getEntityManager();

        $urlAlias = new UrlAlias();
        $urlAlias->setKeyword($keyword)
            ->setQuery("category_id=" . $category->getId())
        ;
        $em->persist($urlAlias);
        $em->flush();
    }

    /**
     *
     */
    private function _insertCategoryAndRelated($data, $category)
    {
        $category = $this->_updateCategory($category, $data);

        $this->_saveDescriptions(
            $data->get('category_description', array()),
            $category
        );

        $filtersData = $data->get('category_filter', array());
        $storesData = $data->get('category_store', array());
        $layoutData = $data->get('category_layout', array());

        //TODO saveFIlter
        $this->_savesFilter($filtersData, $category);
        $this->_savesStores($storesData, $category);
        $this->_savesLayouts($layoutData, $category);

        if (isset($data['keyword'])) {
            $this->_saveKeyword($data['keyword'], $category);
        }
        return $category;
    }

    /**
     *
     */
    private function _deleteRelatedToCategory($categoryId)
    {
        $this->_deleteOfCategory("Entity\CategoryFilter", $categoryId);
        $this->_deleteOfCategory("Entity\CategoryDescription", $categoryId);
        $this->_deleteOfCategory("Entity\CategoryStore", $categoryId);
        $this->_deleteOfCategory("Entity\CategoryLayout", $categoryId);
        $this->_deleteUrlAlias($categoryId);
    }

    /**
     *
     */
    private function _deleteOfCategory($entityString, $categoryId)
    {
        $dql = "
            DELETE
            FROM $entityString e
            WHERE e.categoryId = :categoryId
        ";

        $em = $this->getEntityManager();
        $em->createQuery($dql)
            ->setParameter('categoryId', $categoryId)
            ->execute()
        ;
    }

    /**
     *
     */
    private function _deleteUrlAlias($categoryId)
    {
        $dql = "
            DELETE
            FROM Entity\UrlAlias e
            WHERE e.query = :query
        ";

        $em = $this->getEntityManager();
        $em->createQuery($dql)
            ->setParameter('query', 'category_id=' . $categoryId)
            ->execute()
        ;
    }

    /**
     *
     */
    private function _findAllWithPath()
    {
        // get all categories
        $categories = $this->createQueryBuilder('Entity\Category')
            ->select('c')
            ->distinct()
            ->from('Entity\Category', 'c')
            ->getQuery()
            ->getArrayResult();
        ;

        // emulate the GROUP_CONCAT to set property 'name'
        $that = $this;
        $categories = array_map(
            function ($category) use ($that) {
                $id = $category['id'];
                $category['name'] = $this->getPathString($id);
                return $category;
            },
            $categories
        );
        return $categories;
    }

    /**
     *
     */
    private function _orderBy($categories, $orderBy, $order)
    {
        $orderByComparator = array(
            'name' => 'strcmp',
            //Note: int overflow if $a is too huge
            'sortOrder' => function ($a, $b) {
                return $a-$b;
            }
        );

        // TODO replace by a "get default value if not set" array
        if (!isset($orderByComparator[$orderBy])) {
            $orderBy = 'sortOrder';
        }

        $orderModifiers = array(
            'ASC' => 1,
            'DESC' => -1
        );

        $baseCmp = $orderByComparator[$orderBy];
        $modifier = $orderModifiers[$order];
        
        $comparator = function ($c1, $c2) use ($baseCmp, $modifier, $orderBy) {
            //basically a basecmp is a function that return -1/1/0
            //based on value of field orderBy
            // and modifier ASC 1  / DESC -1 will reverse the order
            return $modifier * $baseCmp($c1[$orderBy], $c2[$orderBy]);
        };
          
        usort($categories, $comparator);

        return $categories;
    }
}
