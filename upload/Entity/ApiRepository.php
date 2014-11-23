<?php
namespace Entity;
use Doctrine\ORM\EntityRepository;

class ApiRepository extends EntityRepository
{
    public function add($username, $password, $status)
    {
        $api = new \Entity\Api();
        $this->_editAndSave($api, $username, $password, $status);
    }

    /**
     *
     */
    public function edit($id, $username, $password, $status)
    {
        $api = $this->find($id);
        $api->setDateModified(new \DateTime());
        $this->_editAndSave($api, $username, $password, $status);
    }

    /**
     *
     */
    public function delete($id)
    {
        $this->getEntityManager()
            ->createQuery('DELETE FROM Entity\Api a WHERE a.id = :id')
            ->setParameter('id', $id)
            ->execute();
    }

    /**
     * TODO: use the Paginated extension from doctrine
     */
    public function findAllPaginatedAsArray(
        $start = 0,
        $limit = 20,
        $orderBy =  'username',
        $order = 'ASC'
    ) {
        $possibleOrderBy = array(
            'username',
            'status',
            'date_added',
            'date_modified'
        );

        if (!in_array($orderBy, $possibleOrderBy)) {
            $orderBy = 'username';
        }
        return $this->createQueryBuilder('Entity\Api')
            ->select('a')
            // TODO: without distinct it returns duplicate result
            // need to investigate why
            ->distinct()
            ->from('Entity\Api', 'a')
            ->orderBy('a.'.$orderBy, $order)
            ->setFirstResult($start)
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     *
     */
    public function findAsArray($id)
    {
        return $this->createQueryBuilder('Entity\Api')
            ->select('a')
            ->from('Entity\Api','a')
            ->where('a.id = :id')
            ->setParameter('id', $id)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     *
     */
    public function count()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT COUNT(a.id) FROM Entity\Api a')
            ->getSingleScalarResult();
    }

    /**
     *
     */
    private function _editAndSave(
        $api,
        $name,
        $password,
        $status
    ) {
        $api->setUsername($name);
        $api->setPassword($password);
        $api->setStatus($status);

        $em = $this->getEntityManager();
        $em->persist($api);
        $em->flush();
    }
}
