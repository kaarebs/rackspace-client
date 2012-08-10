<?php
namespace ClausWitt\Rackspace\Client\Server;

class Client extends \ClausWitt\Rackspace\Client\Client {

    CONST ENDPOINT_SERVER_LIST = 'servers/detail';
    CONST ENDPOINT_SERVER_SPECIFIC = 'servers/';

    CONST ENDPOINT_SERVER_CREATE = 'servers';

    CONST ENDPOINT_FLAVOUR_LIST = 'flavors/detail';
    CONST ENDPOINT_FLAVOUR_SPECIFIC = 'flavors/';

    CONST ENDPOINT_IMAGE_LIST = 'images/detail';
    CONST ENDPOINT_IMAGE_SPECIFIC = 'images/';

    public function createServer($name, $imageId, $flavorId) {
        $data = array('server' => array('name' => $name, 'imageId' => $imageId, 'flavorId' => $flavorId));
        $data = json_encode($data);
        $apiUrl = $this->getApiUrl($this->serverManagementUrl, self::ENDPOINT_SERVER_CREATE);
        $request = $this->getRequestObject($apiUrl, 'post');
        $request->body($data);
        $response = $request->send();
        return $response->body;
    }

    public function getServers() {
        /** @var $response \Httpful\Response */
        $response = $this->callServerManagement(self::ENDPOINT_SERVER_LIST);
        return $response->body->servers;
    }

    public function getServer($id) {
        /** @var $response \Httpful\Response */
        $response = $this->callServerManagement(self::ENDPOINT_SERVER_SPECIFIC . $id);
        return $response->body->server;
    }

    public function getFlavours() {
        /** @var $response \Httpful\Response */
        $response = $this->callServerManagement(self::ENDPOINT_FLAVOUR_LIST);
        return $response->body->flavors;
    }

    public function getFlavour($id) {
        /** @var $response \Httpful\Response */
        $response = $this->callServerManagement(self::ENDPOINT_FLAVOUR_SPECIFIC . $id);
        return $response->body->flavour;
    }

    public function getImages() {
        /** @var $response \Httpful\Response */
        $response = $this->callServerManagement(self::ENDPOINT_IMAGE_LIST);
        return $response->body->images;
    }

    public function getImage($id) {
        /** @var $response \Httpful\Response */
        $response = $this->callServerManagement(self::ENDPOINT_IMAGE_SPECIFIC . $id);
        return $response->body->image;
    }


    /**
     * @param  $ip The public ip of the servers
     * @return object|false
     */
    public function getServerFromIp($ip) {
        foreach($this->getServers() as $server) {
            $ips = $server->addresses->public;
            foreach($ips as $serverIP) {
                if($serverIP == $ip) {
                    return $server;
                }
            }

        }
        return false;
    }


    public function getServerIpsMatching($name) {
        $servers = $this->getServersMatching($name);
        $ips = array();

        foreach ($servers as $server) {
            $ipList = $server->addresses->public;
            foreach($ipList as $serverIP) {
                $ips[] = $serverIP;
            }
        }

        return $ips;
    }

    public function getServerCountMatching($name) {
        return \count($this->getServersMatching($name));
    }

    public function getServersMatching($name) {
        $returnServers = array();

        foreach($this->getServers() as $server) {
            if(\strpos($server->name, $name)!==false) {
                $returnServers[] = $server;
            }
        }
        return $returnServers;
    }
}