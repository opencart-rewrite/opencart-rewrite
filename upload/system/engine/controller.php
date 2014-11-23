<?php
abstract class Controller {
    protected $registry;

    /**
     * doctrine repository of the main Entity
     * of this controller
     */
    protected $repository;

    public function __construct($registry) {
        $this->registry = $registry;
    }

    public function __get($key) {
        return $this->registry->get($key);
    }

    public function __set($key, $value) {
        $this->registry->set($key, $value);
    }

    /**
     * TODO: move me in something specific to extension
     */
    protected function addPermission($route)
    {
        $this->em->getRepository('Entity\UserGroup')->addPermission(
            $this->user->getGroupId(),
            'access',
            $route
        );

        $this->em->getRepository('Entity\UserGroup')->addPermission(
            $this->user->getGroupId(),
            'modify',
            $route
        );
    }

}
