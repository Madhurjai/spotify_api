<?php

use Phalcon\Mvc\Controller;
use GuzzleHttp\Client;
use Guzzle\Http\Exception;

class SearchController extends Controller
{
    public function indexAction()
    {
        // this function is used for searching songs and album playlist etc 



        if ($this->request->getPost('search')) {
            $name = $this->request->get('name');
            if ($this->request->get('album')) {
                $url = "https://api.spotify.com/v1/search?q=" . urlencode($name) . "&type=" . $this->request->get('album') . "";
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Bearer ' . $this->session->get('token')));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $val = curl_exec($ch);
                $v = json_decode($val);
                $this->view->album = $v->albums->items;
            }
            if ($this->request->get('artist')) {
                $url = "https://api.spotify.com/v1/search?q=" . urlencode($name) . "&type=" . $this->request->get('artist') . "";
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Bearer ' . $this->session->get('token')));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $val = curl_exec($ch);
                $v = json_decode($val);

                $this->view->artist = $v->artists->items;
            }
            if ($this->request->get('playlist')) {
                $url = "https://api.spotify.com/v1/search?q=" . urlencode($name) . "&type=" . $this->request->get('playlist') . "";
                $ch = curl_init($url);
                curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Bearer ' . $this->session->get('token')));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                $val = curl_exec($ch);
                $v = json_decode($val);

                $this->view->playlist = $v->playlists->items;
            }



            $url = "https://api.spotify.com/v1/search?q=" . urlencode($name) . "&type=track";
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Bearer ' . $this->session->get('token')));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            $val = curl_exec($ch);
            $v = json_decode($val);


            $this->view->records = $v->tracks->items;
        }
    }
    //this function is used for creating a playlist
    public function createplaylistAction()
    {
        $ch = curl_init();
        $this->view->urls = $this->request->get('urls');
        curl_setopt($ch, CURLOPT_URL, 'https://api.spotify.com/v1/me');
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Bearer ' . $this->session->get('token')));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $userprofile = json_decode(curl_exec($ch));

        if ($this->request->get('create')) {
            if ($userprofile->id) {
                $this->EventManager->fire('tokens:updatetoken', $this);
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

                $this->response->redirect('search');
            }
        }
        if ($this->request->get('urls')) {
            $this->EventManager->fire('tokens:updatetoken', $this);
            $url = 'https://api.spotify.com/v1/users/' . $userprofile->id . '/playlists';
            $ch = curl_init($url);
            curl_setopt($ch, CURLOPT_HTTPGET, 1);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Bearer ' . $this->session->get('token')));
            $playlists = json_decode(curl_exec($ch));

            $this->view->playlist =  $playlists;
        }
    }
    // this function is used for performing CRUD operation in playlist 
    public function myplaylistAction()
    {
        if ($this->request->get('myplaylist')) {
            $this->EventManager->fire('tokens:updatetoken', $this);
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, 'https://api.spotify.com/v1/me/playlists');
            curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json', 'Authorization: Bearer ' . $this->session->get('token')));
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $myplaylist = json_decode(curl_exec($ch));

            $this->view->myplaylist = $myplaylist->items;
        }

        if ($this->request->get('playlist')) {
            $playlist_id = $this->request->get('id');

            $uri = $this->request->get('urls');
            try {
                $client = new Client();
                $result = $client->request('POST', "https://api.spotify.com/v1/playlists/$playlist_id/tracks?uris=$uri", [
                    'headers' => [
                        'Authorization' => "Bearer " . $this->session->token
                    ]
                ]);
            } catch (ClientException $e) {
                $this->EventManager->fire('tokens:updatetoken', $this);
            }



            $this->response->redirect('search');
        }
    }
    //this function is used for display songs in playlist
    public function displayPlaylistAction()
    {
        if ($this->request->get('playlist')) {

            $playlist_id = $this->request->get('id');
            $this->view->playlist_id =  $this->request->get('id');
            try {
                $client = new Client();
                $val = $client->get("https://api.spotify.com/v1/playlists/" . $playlist_id . "/tracks", [
                    'headers' => [
                        'Authorization' => "Bearer " . $this->session->token
                    ]
                ]);
                $v = json_decode($val->getBody());
            } catch (ClientException $e) {
                $this->EventManager->fire('tokens:updatetoken', $this);
            }

            $this->view->items = $v->items;
        }
        if ($this->request->get('remove')) {
            $playlist_id = $this->request->get('id');

            $uri = $this->request->get('uris');
            try {
                $client = new Client();
                $result = $client->delete("https://api.spotify.com/v1/playlists/" . $playlist_id . "/tracks", [
                    'headers' => [
                        'Authorization' => "Bearer " . $this->session->token
                    ],
                    'body' => json_encode([
                        'uris' => [$uri]
                    ])
                ]);
            } catch (ClientException $e) {
                $this->EventManager->fire('tokens:updatetoken', $this);
            }
            $this->response->redirect('search');
        }
    }
}
