<?php

use Phalcon\Mvc\Controller;

class SearchController extends Controller
{
    public function indexAction()
    {
        if ($this->request->getPost('search')) {
            $name = $this->request->get('name');
            $arr = $this->request->get('arr');


            $val = '';
            for ($i = 0; $i < count($arr); $i++) {
                $val .= '&type=' . $arr[0] . '';
            }

            $url = "https://api.spotify.com/v1/search?q=" . urlencode($name) . $val . "";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Bearer ' . $this->session->get('token')));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            // curl_setopt($ch, CURLOPT_URL, $url);
            $val = curl_exec($ch);
            $v = json_decode($val);
            // echo "<pre>";
            // print_r($v);
            // die ;
            $this->view->records = $v->tracks->items;
        }
    }

    public function createplaylistAction()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'https://api.spotify.com/v1/me');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Bearer ' . $this->session->get('token')));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userprofile = json_decode(curl_exec($ch));
        if ($this->request->get('create')) {

            if ($userprofile->id) {
                $url = 'https://api.spotify.com/v1/users/' . $userprofile->id . '/playlists';
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Bearer ' . $this->session->get('token')));

                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt(
                    $ch,
                    CURLOPT_POSTFIELDS,
                    "{\"name\":\"" . $this->request->get('playlist') . "\",\"description\":\"New playlist description\",\"public\":false}"
                );
                $server_output = json_decode(curl_exec($ch));
            }
        }
        $url = 'https://api.spotify.com/v1/users/' . $userprofile->id . '/playlists';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $this->session->get('token')));
        $playlists = json_decode(curl_exec($ch));
        $this->view->playlist =  $playlists;
        // echo "<pre>";
        // print_r($playlists);
        // die;
    }
}
