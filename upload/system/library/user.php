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

        //TODO use a repository for user
        $user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE user_id = '$userId' AND status = '1'");

        if (!$user_query->num_rows) {
            $this->logout();
            return;
        }
        $this->user_id = $user_query->row['user_id'];
        $this->username = $user_query->row['username'];

        $this->db->query("UPDATE " . DB_PREFIX . "user SET ip = '" . $this->db->escape($this->request->server['REMOTE_ADDR']) . "' WHERE user_id = '$userId'");

        $this->_setPermissions($user_query);
    }

    public function login($username, $password) {
        $user_query = $this->db->query("SELECT * FROM " . DB_PREFIX . "user WHERE username = '" . $this->db->escape($username) . "' AND (password = SHA1(CONCAT(salt, SHA1(CONCAT(salt, SHA1('" . $this->db->escape($password) . "'))))) OR password = '" . $this->db->escape(md5($password)) . "') AND status = '1'");

        if (!$user_query->num_rows) {
            return false;
        }
        $this->session->data['user_id'] = $user_query->row['user_id'];

        $this->user_id = $user_query->row['user_id'];
        $this->username = $user_query->row['username'];

        $this->_setPermissions($user_query);

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

    /**
     * Set permissions of current user
     */
    private function _setPermissions($userQuery)
    {
        // TODO replace this by a "User" entity
        $groupId = (int)$userQuery->row['user_group_id'];
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
