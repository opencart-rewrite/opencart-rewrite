<?php
namespace Entity;
use Doctrine\ORM\EntityRepository;
use Entities;

class UserRepository extends EntityRepository
{
    public function add(array $data)
    {
        $user = new Entity\User();

    }

    /**
     *
     */
    public function edit(
        $id,
        $data
    ) {
        $user = $this->find($id);
        $this->_editAndSave($user, $data);

    }

    /**
     * Change the password of a given user by new one
     * in the meantime, reset also the salt and forget password code
     * of this user
     *
     * @param string $userId   Id of the user to edit the password of
     * @param string $password New password to hash, salt and save
     *
     * @return void
     */
    public function editPassword($userId, $password)
    {
        $salt = $this->_generateSalt();
        $hashedPassword = $this->_hashPasswordWithSalt($password, $salt);

        $this->getEntityManager()
            ->createQuery(
                '
                UPDATE Entity\User u
                SET
                    u.code = "",
                    u.password =  :password,
                    u.salt = :salt
                WHERE u.id = :id
                '
            )
            ->setParameter('password', $password)
            ->setParameter('salt', $salt)
            ->setParameter('id', $userId)
            ->execute();
    }

    /**
     * Update last known ip of a given user
     *
     * @param string $userId Id of the user
     * @param string $ip     New ip
     *
     * @return void
     */
    public function updateIp($userId, $ip)
    {
        $this->getEntityManager()
            ->createQuery(
                '
                UPDATE Entity\User u
                SET
                    u.ip = :ip
                WHERE u.id = :id
                '
            )
            ->setParameter('ip', $ip)
            ->setParameter('id', $userId)
            ->execute();
    }

    /**
     * Get the user id of a user identified by a
     * forget password code
     *
     * @param string $code forget password code
     *
     * @return string (or null if no such user)
     */
    public function getUserIdByCode($code)
    {

        return $this->getEntityManager()

            // min is to force a result to be
            // returned even if no row match
            ->createQuery(
                '
                SELECT MIN(u.id)
                FROM Entity\User u
                WHERE u.code = :code
                '
            )
            ->setParameter('code', $code)
            ->setMaxResults(1)
            ->getSingleScalarResult();
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
        $possibleOrderBy = array(
            'username',
            'status',
            'date_added'
        );

        if (!in_array($orderBy, $possibleOrderBy)) {
            $orderBy = 'name';
        }

        return $this->createQueryBuilder('Entity\User')
            ->select('u')
            ->from('Entity\User', 'u')
            ->orderBy('u.'.$orderBy, $order)
            ->setFirstResult($start)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     * Delete user by given id
     *
     * @param string $userId id of the user to delete
     *
     * @return void
     */
    public function delete($userId)
    {
        $this->getEntityManager()
            ->createQuery('DELETE FROM Entity\User u WHERE u.id = :id')
            ->setParameter('id', $userId)
            ->execute();
    }

    /**
     *
     */
    public function count()
    {
        return $this->getEntityManager()
            ->createQuery('SELECT COUNT(u.id) FROM Entity\User u')
            ->getSingleScalarResult();
    }

    /**
     * Get number of users in a given group
     *
     * @param string $groupId id of the group we want the number of user
     *
     * @return integer
     */
    public function countByGroupId($groupId)
    {
        return $this->getEntityManager()
            ->createQuery(
                '
                SELECT COUNT(u.id)
                FROM Entity\User u
                WHERE u.groupId = :groupId
                '
            )
            ->setParameter('groupId',(int) $groupId)
            ->getSingleScalarResult();
    }

    /**
     * Try to login with user and return it if login successful
     * 
     * @param string $username
     * @param string $password
     *
     * @return User or null if login incorrect
     */
    public function login($username, $password)
    {
        $user = $this->findOneByUsername($username);
        if (is_null($user)) {
            return null;
        }

        $hashedPassword = $this->_hashPasswordWithSalt(
            $password,
            $user->getSalt()
        );

        if (
            !$user->isActivated() ||
            (
                $user->getHashedPassword() !== $hashedPassword &&
                // it was in original opencart, certainly for
                // legacy purpose
                $user->getHashedPassword() !== md5($password)
            )
        ) {
            return null;
        }
        return $user;
    }

    /**
     * Check if email is already existing in database
     *
     * @param string $email email to check
     *
     * @return boolean
     */
    public function isEmailAlreadyTaken($email)
    {
        $email = utf8_strtolower($email);

        $count = $this->getEntityManager()
            ->createQuery(
                '
                SELECT COUNT(u.id)
                FROM Entity\User u
                WHERE u.email = :email
                '
            )
            ->setParameter('email', $email)
            ->getSingleScalarResult();
        return $count !== 0;
    }

    /**
     * generate a 'forgot password code' for a user, given the
     * user's email
     *
     * @param string $email email to use to identify user
     *
     * @return void
     */
    public function generateForgetPasswordCode($email)
    {
        $code = sha1(uniqid(mt_rand(), true));

        $email = utf8_strtolower($email);

        $this->getEntityManager()
            ->createQuery(
                '
                UPDATE Entity\User u
                SET u.code = :code
                WHERE u.email = :email
                '
            )
            ->setParameter('code', $code)
            ->setParameter('email', $email)
            ->execute();
    }

    /**
     * Get the user id of a user identified by a
     * username
     *
     * @param string $username name of the user we're searching
     *
     * @return string (or null if no such user)
     */
    public function getIdByUsername($username)
    {

        return $this->getEntityManager()
            // min is to force a result to be
            // returned even if no row match
            ->createQuery(
                '
                SELECT MIN(u.id)
                FROM Entity\User u
                WHERE u.username = :username
                '
            )
            ->setParameter('username', $username)
            ->getSingleScalarResult();
    }

    /**
     * Get the user id of a user identified by a
     * forget password code
     *
     * @param string $code forget password code
     *
     * @return string (or null if no such user)
     */
    public function getIdByCode($code)
    {

        return $this->getEntityManager()

            // min is to force a result to be
            // returned even if no row match
            ->createQuery(
                '
                SELECT MIN(u.id)
                FROM Entity\User u
                WHERE u.code = :code
                '
            )
            ->setParameter('code', $code)
            ->setMaxResults(1)
            ->getSingleScalarResult();
    }

    /**
     *
     */
    private function _editAndSave($user, $data)
    {
        $user->setUsername($data['username']);
        $user->setGroupId((int) $data['user_group_id']);
        $user->setPassword($data['password']);
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);
        // we store emails as lower case
        // as anyway, elsewhere in the application
        // we do comparison using lower case
        $user->setEmail(
            utf8_strtolower($data['email'])
        );
        $user->setImage($data['image']);
        $user->setStatus(((int)$data['status']) === 1);

        $em = $this->getEntityManager();
        $em->persist($user);
        $em->flush();
    }

    /**
     *
     */
    private function _generateSalt()
    {
        return substr(md5(uniqid(rand(), true)), 0, 9);
    }

    /**
     *
     */
    private function _hashPasswordWithSalt($password, $salt)
    {
        return sha1($salt . sha1($salt . sha1($password)));
    }
}
