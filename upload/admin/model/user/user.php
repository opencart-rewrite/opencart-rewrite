<?php
class ModelUserUser extends Model {
    public function addUser($data)
    {
        $user = new Entity\User();
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

        $this->em->persist($user);
        $this->em->flush();
    }

    /**
     *
     */
    public function editUser($userId, $data)
    {

        $this->em
            ->createQuery(
                '
                UPDATE Entity\User u
                SET
                    u.username =  :username,
                    u.groupId =  :groupId,
                    u.firstname =  :firstname,
                    u.lastname = :lastname,
                    u.email = :email,
                    u.image = :image,
                    u.status = :status
                WHERE u.id = :id
                '
            )
            ->setParameter('username', $data['username'])
            ->setParameter('groupId', (int) $data['user_group_id'])
            ->setParameter('firstname', $data['firstname'])
            ->setParameter('lastname', $data['lastname'])
            ->setParameter('email', $data['email'])
            ->setParameter('image', $data['image'])
            ->setParameter('status', (int) $data['status'])
            ->setParameter('id', (int) $userId)
            ->execute();

        if ($data['password']) {
            $this->editPassword($userId, $data['password']);
        }
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

        $this->em
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

        $this->em
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
     * Delete user by given id
     *
     * @param string $userId id of the user to delete
     *
     * @return void
     */
    public function deleteUser($userId)
    {
        $this->em
            ->createQuery('DELETE FROM Entity\User u WHERE u.id = :id')
            ->setParameter('id', $userId)
            ->execute();
    }

    public function getUser($id) {
        return $this->em->getRepository('Entity\User')->find($id);
    }

    /**
     * Get the user id of a user identified by a
     * username
     *
     * @param string $username name of the user we're searching
     *
     * @return string (or null if no such user)
     */
    public function getUserIdByUsername($username)
    {

        return $this->em
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
    public function getUserIdByCode($code)
    {

        return $this->em

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

    public function getUsers($data = array()) {

        $sort_data = array(
            'username',
            'status',
            'date_added'
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
            ->from('Entity\User', 'u')
            ->orderBy('u.'.$orderBy, $order)
            ->setFirstResult($start)
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    /**
     *
     */
    public function getTotalUsers()
    {
        return $this->em
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
    public function getTotalUsersByGroupId($groupId)
    {
        return $this->em
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
     * Check if email is already existing in database
     *
     * @param string $email email to check
     *
     * @return boolean
     */
    public function isEmailAlreadyTaken($email)
    {
        $email = utf8_strtolower($email);

        $count = $this->em
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
