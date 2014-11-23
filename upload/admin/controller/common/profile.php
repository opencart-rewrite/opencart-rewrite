<?php
class ControllerCommonProfile extends Controller {

    public function __construct($registry)
    {
        parent::__construct($registry);
        $this->repository = $registry->get('em')->getRepository(
            'Entity\User'
        );
    }

    public function index() {
        $this->load->language('common/menu');

        $this->load->model('tool/image');

        $user = $this->repository->find($this->user->getId());

        if ($user) {
            $data['firstname'] = $user->getFirstname();
            $data['lastname'] = $user->getLastname();
            $data['username'] = $user->getUsername();

            //TODO replace by something better when we will have
            // UserGroups converted into a doctrine's Entity
            $group = $this->em->getRepository('Entity\UserGroup')->find(
                $user->getGroupId()
            );
            $data['user_group'] = $group->getName();

            $userImage = $user->getImage();

            if (is_file(DIR_IMAGE . $userImage)) {
                $data['image'] = $this->model_tool_image->resize($userImage, 45, 45);
            } else {
                $data['image'] = $this->model_tool_image->resize('no_image.png', 45, 45);
            }
        } else {
            $data['username'] = '';
            $data['image'] = '';
        }

        return $this->load->view('common/profile.tpl', $data);
    }
}
