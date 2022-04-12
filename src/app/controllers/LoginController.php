<?php

use Phalcon\Mvc\Controller;


class LoginController extends Controller
{
    public function indexAction()
    {
        if ($this->request->get('email')) {
            $email = $this->request->get('email');
            $password = $this->request->get('password');
            $item = Users::find('email= "' . $email . '" AND password = "' . $password . '"');
            if (isset($item[0]->email)) {

                $this->session->user = $item[0]->email;
                
                if (isset($item[0]->token)) {
                    $this->session->token = $item[0]->token;
                    $this->response->redirect('/dashboard');
                } else {
                    $this->response->redirect('/');
                }
            } else {
                echo "invalid creadential !!";
            }
        }
    }
}
