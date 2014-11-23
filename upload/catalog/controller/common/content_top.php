<?php
class ControllerCommonContentTop extends Controller {
    public function index() {
        $this->load->model('design/layout');

        if (isset($this->request->get['route'])) {
            $route = (string)$this->request->get['route'];
        } else {
            $route = 'common/home';
        }

        $layout_id = 0;

        if ($route == 'product/category' && isset($this->request->get['path'])) {
            $this->load->model('catalog/category');

            $path = explode('_', (string)$this->request->get['path']);

            $layout_id = $this->model_catalog_category->getCategoryLayoutId(end($path));
        }

        if ($route == 'product/product' && isset($this->request->get['product_id'])) {
            $this->load->model('catalog/product');

            $layout_id = $this->model_catalog_product->getProductLayoutId($this->request->get['product_id']);
        }

        if ($route == 'information/information' && isset($this->request->get['information_id'])) {
            $this->load->model('catalog/information');

            $layout_id = $this->model_catalog_information->getInformationLayoutId($this->request->get['information_id']);
        }

        if (!$layout_id) {
            $layout_id = $this->model_design_layout->getLayout($route);
        }

        if (!$layout_id) {
            $layout_id = $this->config->get('config_layout_id');
        }

        $data['modules'] = array();

        $modules = $this->model_design_layout->getLayoutModules($layout_id, 'content_top');

        foreach ($modules as $module) {
            $setting = unserialize($module['setting']);

            if (!empty($setting['status'])) { 
                $data['modules'][] = $this->load->controller('module/' . $module['code'], $setting);
            }
        }

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/common/content_top.tpl')) {
            return $this->load->view($this->config->get('config_template') . '/template/common/content_top.tpl', $data);
        } else {
            return $this->load->view('default/template/common/content_top.tpl', $data);
        }
    }
}