<?php

use Phalcon\Mvc\Controller;

class SignupController extends Controller{

    public function IndexAction(){

    }

    public function registerAction(){
        $user = new Users();
        $email = $this->request->getPost('email');
        $val = Users::find('email = "'.$email.'"');
       
        if(isset($val[0]->email)) {
            $this->view->err = "email already exist !!" ;
        } else {
        $user->assign(
            $this->request->getPost(),
            [
                'name',
                'email',
                'password'
            ]
        );

        $success = $user->save();

        $this->view->success = $success;
    }
    
            if($success){
                $this->view->message = "Register succesfully";
            }else{
                $this->view->message = "Not Register succesfully due to following reason: <br>".implode("<br>", $user->getMessages());
            }
    }
}