<?php 
namespace App\components ;
use Phalcon\Di\Injectable;
use Phalcon\Events\Event;
use Users;

class Helperclass extends Injectable {
    public function updatetoken(Event $event,$v) {
        
        $user = Users::find('email= "'.$this->session->get('user').'"');
        // print_r($user);
        // die ;
        $data = array(
            'redirect_uri' => 'http://localhost:8080',
            'grant_type'   => 'refresh_token',
            'refresh_token'  => $user[0]->refresh_token
        );
        $client_id = 'a268440d99e54e388dc6d9daee5118f9' ;
        $client_secret = '26ff2e321595418788790bd8948d1c4d' ;
        $ch = curl_init();
        curl_setopt( $ch, CURLOPT_URL, 'https://accounts.spotify.com/api/token' );
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1 );
        curl_setopt( $ch, CURLOPT_POST, 1 );
        curl_setopt( $ch, CURLOPT_POSTFIELDS, http_build_query( $data ) );
        curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/x-www-form-urlencoded','Authorization: Basic ' . base64_encode( $client_id . ':' . $client_secret ) ) );
    
        $result = json_decode( curl_exec( $ch ) );
        $user[0]->token = $result->access_token ;
        $this->session->token = $result->access_token ;
        $user[0]->update();
        // return $result->access_token ;
        
    }
}