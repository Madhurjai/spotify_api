<?php

use Phalcon\Mvc\Controller;
use GuzzleHttp\Client;


class DashboardController extends Controller
{ 
    public function indexAction() {
        
        $url = "https://api.spotify.com/v1/me";
      
            $this->EventManager->fire('tokens:updatetoken', $this) ;
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json','Authorization: Bearer ' . $this->session->get('token')));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $val = curl_exec($ch);
            $v = json_decode($val);
        
        $this->view->details = $v ;
    }
}