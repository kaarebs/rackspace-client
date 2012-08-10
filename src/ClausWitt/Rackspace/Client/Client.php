<?php
namespace ClausWitt\Rackspace\Client;
use \Httpful\Request;

class Client {
    CONST AUTH_URL = 'https://auth.api.rackspacecloud.com/v1.0';

    CONST HEADER_USER = 'X-Auth-User';
    CONST HEADER_KEY = 'X-Auth-Key';

    CONST HEADER_AUTH_TOKEN = 'X-Auth-Token';
    CONST HEADER_STORAGE_URL = 'X-Storage-Url';
    CONST HEADER_STORAGE_TOKEN = 'X-Storage-Token';
    CONST HEADER_SERVER_MANAGEMENT_URL = 'X-Server-Management-Url';
    CONST HEADER_CDN_MANAGEMENT_URL = 'X-CDN-Management-Url]';

    protected $user;
    protected $apiKey;
    protected $storageUrl;
    protected $serverManagementUrl;
    protected $cdnManagementUrl;
    protected $authToken;
    protected $storageToken;

    public function __construct($user = '', $apiKey = '', $autoAuthenticate = true) {
        \Httpful\Httpful::register('application/json', new \Httpful\Handlers\JsonHandler());

        $this->user = $user;
        $this->apiKey = $apiKey;
        if($autoAuthenticate) $this->authenticate();
    }

    public function authenticate() {
        $response = Request::get(self::AUTH_URL)
            ->addHeader(self::HEADER_USER, $this->user)
            ->addHeader(self::HEADER_KEY, $this->apiKey)
            ->send();

        $statusCode = $response->code;
        if ($statusCode < 200 || $statusCode > 299) {
            throw new \Exception('Error logging in to rackspace cloud');
        }
        foreach ($response->headers as $headerName => $value) {
            switch ($headerName) {
                case self::HEADER_AUTH_TOKEN:
                    $this->authToken = $value;
                    break;
                case self::HEADER_STORAGE_URL:
                    $this->storageUrl = $value;
                    break;
                case self::HEADER_STORAGE_TOKEN:
                    $this->storageToken = $value;
                    break;
                case self::HEADER_SERVER_MANAGEMENT_URL:
                    $this->serverManagementUrl = $value;
                    break;
                case self::HEADER_CDN_MANAGEMENT_URL:
                    $this->cdnManagementUrl = $value;
                    break;
                default:
                    break;
            }
        }
        return $this;
    }

    public function setApiKey($apiKey) {
        $this->apiKey = $apiKey;
    }

    public function getApiKey() {
        return $this->apiKey;
    }

    public function setAuthToken($authToken) {
        $this->authToken = $authToken;
    }

    public function getAuthToken() {
        return $this->authToken;
    }

    public function setCdnManagementUrl($cdnManagementUrl) {
        $this->cdnManagementUrl = $cdnManagementUrl;
    }

    public function getCdnManagementUrl() {
        return $this->cdnManagementUrl;
    }

    public function setServerManagementUrl($serverManagementUrl) {
        $this->serverManagementUrl = $serverManagementUrl;
    }

    public function getServerManagementUrl() {
        return $this->serverManagementUrl;
    }

    public function setStorageToken($storageToken) {
        $this->storageToken = $storageToken;
    }

    public function getStorageToken() {
        return $this->storageToken;
    }

    public function setStorageUrl($storageUrl) {
        $this->storageUrl = $storageUrl;
    }

    public function getStorageUrl() {
        return $this->storageUrl;
    }

    public function setUser($user) {
        $this->user = $user;
    }

    public function getUser() {
        return $this->user;
    }

    public function callServerManagement($endPoint, $additionalHeaders=array()) {
        $apiUrl = $this->getApiUrl($this->serverManagementUrl, $endPoint);
        return $this->callApi($apiUrl, $additionalHeaders);
    }

    private function callApi($url, $additionalHeaders=array()) {
        $request = $this->getRequestObject($url);

        foreach($additionalHeaders as $key=>$val) {
            $request->addHeader($key, $val);
        }

        return $request->send();
    }

    /**
     * @param $url
     * @return \Httpful\Request
     */
    protected function getRequestObject($url, $type='get') {

        switch($type) {
            case 'post':
                $request = $this->getRequestPostObject($url);
                break;
            case 'put':
                $request = $this->getRequestPutObject($url);
                break;
            case 'delete':
                $request = $this->getRequestDeleteObject($url);
                break;
            case 'get':
            default:
                $request = $this->getRequestGetObject($url);
            break;
        }
        $request->expectsType('application/json');
        $request->addHeader(self::HEADER_AUTH_TOKEN, $this->authToken);
        return $request;
    }

    protected function getRequestGetObject($url) {
        return Request::get($url);
    }

    protected function getRequestPostObject($url) {
        return Request::post($url);
    }

    protected function getRequestPutObject($url) {
        return Request::put($url);
    }

    protected function getRequestDeleteObject($url) {
        return Request::delete($url);
    }

    protected function getApiUrl($baseUrl, $endPoint) {
        return $baseUrl . '/' . $endPoint;
    }

}

