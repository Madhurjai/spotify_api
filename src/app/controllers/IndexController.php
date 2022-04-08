<?php

use Phalcon\Mvc\Controller;


class IndexController extends Controller
{
    public function indexAction()
    {
        
        $data = array(
            'response_type' => 'code',
            'client_id'     => 'a268440d99e54e388dc6d9daee5118f9' ,
            'scope'         => 'playlist-read-private playlist-modify-private',
            'redirect_uri'  => 'http://localhost:8080' 
        );

        $this->view->oauth_url = 'https://accounts.spotify.com/authorize?' . http_build_query( $data );

        if ($this->request->get('code')) {
            $data = array(
                'redirect_uri' => 'http://localhost:8080',
                'grant_type'   => 'authorization_code',
                'code'         => $this->request->get('code')
            );
            $client_id = 'a268440d99e54e388dc6d9daee5118f9' ;
            $client_secret = '26ff2e321595418788790bd8948d1c4d' ;
            $ch = curl_init();
            curl_setopt( $ch, CURLOPT_URL, 'https://accounts.spotify.com/api/token' );
            curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
            curl_setopt( $ch, CURLOPT_POST, 1 );
            curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );
            curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Authorization: Basic ' . base64_encode( $client_id . ':' . $client_secret ) ) );
        
            $result = json_decode( curl_exec( $ch ) );
            // print_r($result);
            // die ;
            $this->session->token = $result->access_token ;
         
            header("Location: http://localhost:8080/search?access=".$result->access_token);
        }

    }
}
