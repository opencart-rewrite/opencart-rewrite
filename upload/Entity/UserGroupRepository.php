<?php
namespace Entity;
use Doctrine\ORM\EntityRepository;
use Entities;

class UserGroupRepository extends EntityRepository
{
    public function add($name, array $permission)
    {
        $group = new Entity\UserGroup();
        $this->_editAndSave($group, $name, $permission);
    }

    /**
     *
     */
    public function edit(
        $id,
        $name,
        $permission
    ) {
        $group = $this->find($id);

        $this->_editAndSave($group, $name, $permission);
    }

    /**
     *
     */
    public function delete($id)
    {
        $this->createQuery('DELETE FROM Entity\UserGroup g WHERE g.id = :id')
            ->setParameter('id', $id)
            ->execute();
    }

    public function findAsArray($id)
    {
        $groupArray = $this->createQueryBuilder('Entity\UserGroup')
            ->select('g')
            ->from('Entity\UserGroup', 'g')
            ->where('g.id = :id')
            ->setParameter('id', (int) $id)
            ->getQuery()
            ->getArrayResult();

        return empty($groupArray) ?
            null : 
            $groupArray[0]
        ;
        
    }

    /**
     * TODO: use the Paginated extension from doctrine
     */
    public function findAllPaginated(
        $start = 0,
        $limit = 20,
        $orderBy =  'name',
        $order = 'ASC'
    ) {
        return $this->createQueryBuilder('Entity\UserGroup')
            ->select('g')
            // TODO: without distinct it returns duplicate result
            // need to investigate why
            ->distinct()
            ->from('Entity\UserGroup', 'g')
            ->orderBy('g.'.$orderBy, $order)
            ->setFirstResult($start)
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     *
     */
    public function count()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT COUNT(g.id) FROM Entity\UserGroup g')
            ->getSingleScalarResult();
    }

    /**
     *
     */
    private function _editAndSave(
        $group,
        $name,
        $permission
    ) {
        $group->setName($name);
        $group->setPermission($permission);

        $em = $this->getEntityManager();
        $em->persist($group);
        $em->flush();
    }
}
