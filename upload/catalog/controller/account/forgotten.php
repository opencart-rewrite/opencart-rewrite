<?php
class ControllerAccountForgotten extends Controller {
    private $error = array();

    public function index() {
        if ($this->customer->isLogged()) {
            $this->response->redirect($this->url->link('account/account', '', 'SSL'));
        }

        $this->load->language('account/forgotten');

        $this->document->setTitle($this->language->get('heading_title'));

        $this->load->model('account/customer');

        if (($this->request->server['REQUEST_METHOD'] == 'POST') && $this->validate()) {
            $this->load->language('mail/forgotten');

            $password = substr(sha1(uniqid(mt_rand(), true)), 0, 10);

            $this->model_account_customer->editPassword($this->request->post['email'], $password);

            $subject = sprintf($this->language->get('text_subject'), $this->config->get('config_name'));

            $message  = sprintf($this->language->get('text_greeting'), $this->config->get('config_name')) . "\n\n";
            $message .= $this->language->get('text_password') . "\n\n";
            $message .= $password;

            $mail = new Mail($this->config->get('config_mail'));
            $mail->setTo($this->request->post['email']);
            $mail->setFrom($this->config->get('config_email'));
            $mail->setSender($this->config->get('config_name'));
            $mail->setSubject($subject);
            $mail->setText(html_entity_decode($message, ENT_QUOTES, 'UTF-8'));
            $mail->send();

            $this->session->data['success'] = $this->language->get('text_success');

            // Add to activity log
            $customer_info = $this->model_account_customer->getCustomerByEmail($this->request->post['email']);

            if ($customer_info) {
                $this->load->model('account/activity');

                $activity_data = array(
                    'customer_id' => $customer_info['customer_id'],
                    'name'        => $customer_info['firstname'] . ' ' . $customer_info['lastname']
                );

                $this->model_account_activity->addActivity('forgotten', $activity_data);
            }

            $this->response->redirect($this->url->link('account/login', '', 'SSL'));
        }

        $data['breadcrumbs'] = array();

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_home'),
            'href' => $this->url->link('common/home')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_account'),
            'href' => $this->url->link('account/account', '', 'SSL')
        );

        $data['breadcrumbs'][] = array(
            'text' => $this->language->get('text_forgotten'),
            'href' => $this->url->link('account/forgotten', '', 'SSL')
        );

        $data['heading_title'] = $this->language->get('heading_title');

        $data['text_your_email'] = $this->language->get('text_your_email');
        $data['text_email'] = $this->language->get('text_email');

        $data['entry_email'] = $this->language->get('entry_email');

        $data['button_continue'] = $this->language->get('button_continue');
        $data['button_back'] = $this->language->get('button_back');

        if (isset($this->error['warning'])) {
            $data['error_warning'] = $this->error['warning'];
        } else {
            $data['error_warning'] = '';
        }

        $data['action'] = $this->url->link('account/forgotten', '', 'SSL');

        $data['back'] = $this->url->link('account/login', '', 'SSL');

        $data['column_left'] = $this->load->controller('common/column_left');
        $data['column_right'] = $this->load->controller('common/column_right');
        $data['content_top'] = $this->load->controller('common/content_top');
        $data['content_bottom'] = $this->load->controller('common/content_bottom');
        $data['footer'] = $this->load->controller('common/footer');
        $data['header'] = $this->load->controller('common/header');

        if (file_exists(DIR_TEMPLATE . $this->config->get('config_template') . '/template/account/forgotten.tpl')) {
            $this->response->setOutput($this->load->view($this->config->get('config_template') . '/template/account/forgotten.tpl', $data));
        } else {
            $this->response->setOutput($this->load->view('default/template/account/forgotten.tpl', $data));
        }
    }

    protected function validate() {
        if (!isset($this->request->post['email'])) {
            $this->error['warning'] = $this->language->get('error_email');
        } elseif (!$this->model_account_customer->getTotalCustomersByEmail($this->request->post['email'])) {
            $this->error['warning'] = $this->language->get('error_email');
        }

        return !$this->error;
    }
}