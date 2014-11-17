<?php
class ModelUserApi extends Model {
    public function addApi($data) {

        $api = new Entity\Api();
        $api->setUsername($data['username']);
        $api->setPassword($data['password']);
        $api->setStatus(((int)$data['status']) === 1);

        $this->em->persist($api);
        $this->em->flush();
    }

    /**
     *
     */
    public function editApi($id, $data)
    {
        $status = ((int)$data['status']) === 1;

        $this->em
            ->createQuery(
                '
                UPDATE Entity\Api a
                SET
                    a.username = :username,
                    a.password =  :password,
                    a.dateModified = :dateModified,
                    a.status = :status
                WHERE a.id = :id
                '
            )
            ->setParameter('username', $data['username'])
            ->setParameter('password', $data['password'])
            ->setParameter('dateModified', new \DateTime())
            ->setParameter('status', $status)
            ->setParameter('id', $id)
            ->execute();

    }

    /**
     * Delete api user by given id
     *
     * @param string $id Id of the api user to delete
     *
     * @return void
     */
    public function deleteApi($id)
    {
        $this->em
            ->createQuery('DELETE FROM Entity\Api u WHERE u.id = :id')
            ->setParameter('id', $id)
            ->execute();
    }



    /**
     *
     */
    public function getApiAsArray($id)
    {
        return $this->em->createQueryBuilder()
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
    public function getApisAsArray($data = array())
    {
        $sql = "SELECT * FROM `" . DB_PREFIX . "api`";

        $sort_data = array(
            'username',
            'status',
            'date_added',
            'date_modified'
        );

        $orderBy = 'username';
        if (isset($data['sort']) && in_array($data['sort'], $sort_data)) {
            $orderby = $data['sort'];
        }

        $order = 'ASC';
        if (isset($data['order']) && ($data['order'] == 'DESC')) {
            $order = "DESC";
        }

        $start = 0;
        $limit = 20;

        if (isset($data['start']) || isset($data['limit'])) {
            $start = $data['start'];
            $limit = $data['limit'];
        }
        return $this->em->createQueryBuilder()
            ->select('u')
            ->from('Entity\Api', 'u')
            ->orderBy('u.'.$orderBy, $order)
            ->setFirstResult($start)
            ->setMaxResults($limit)
            ->getQuery()
            ->getArrayResult();
    }

    /**
     *
     */
    public function getTotalApis()
    {

        return $this->em
            ->createQuery('SELECT COUNT(u.id) FROM Entity\Api u')
            ->getSingleScalarResult();
    }
}
