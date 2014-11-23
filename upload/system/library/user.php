<?php
class User {
    private $user_id;
    private $username;
    private $permission = array();

    public function __construct($registry) {
        $this->db = $registry->get('db');
        $this->em = $registry->get('em');
        $this->request = $registry->get('request');
        $this->session = $registry->get('session');

        if (!isset($this->session->data['user_id'])) {
            return;
        }
        $userId = (int) $this->session->data['user_id'];

        $user = $this->em->getRepository('Entity\User')->find($userId);

        if (is_null($user) || !$user->isActivated()) {
            $this->logout();
            return;
        }
        $this->em->getRepository('Entity\User')->updateIp(
            $userId,
            $this->request->server['REMOTE_ADDR']
        );

        $this->user_id = $user->getId();
        $this->username = $user->getUserName();

        $this->_setPermissions($user->getGroupId());
    }

    public function login($username, $password)
    {
        $user = $this->em->getRepository('Entity\User')->login(
            $username,
            $password
        );

        if (is_null($user)) {
            return false;
        }
        $this->session->data['user_id'] = $user->getId();

        $this->user_id = $user->getId();
        $this->username = $user->getUserName();
        $this->user_group_id = $user->getGroupId();

        $this->_setPermissions($user->getGroupId());

        return true;
    }

    public function logout() {
        unset($this->session->data['user_id']);

        $this->user_id = '';
        $this->username = '';
    }

    public function hasPermission($key, $value) {
        if (isset($this->permission[$key])) {
            return in_array($value, $this->permission[$key]);
        } else {
            return false;
        }
    }

    public function isLogged() {
        return $this->user_id;
    }

    public function getId() {
        return $this->user_id;
    }

    public function getUserName() {
        return $this->username;
    }

    public function getGroupId() {
        return $this->user_group_id;
    }

    /**
     * Set permissions of current user
     */
    private function _setPermissions($groupId)
    {
        $permissions = $this->em
            ->getRepository('Entity\UserGroup')
            ->findAsArray($groupId)['permission']
        ;

        if (!is_array($permissions)) {
            return;
        }
        foreach ($permissions as $key => $value) {
            $this->permission[$key] = $value;
        }

    }
}
