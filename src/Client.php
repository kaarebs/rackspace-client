<?php
namespace Rackspace\Client;
use \Httpful\Request;
class Client {
  CONST AUTH_URL = 'https://auth.api.rackspacecloud.com/v1.0';
  CONST HEADER_USER = 'X-Auth-User';
  CONST HEADER_KEY = 'X-Auth-Key';

  CONST HEADER_AUTH_TOKEN = 'X-auth-token';
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

  public function __construct($user='', $apiKey='') {
    $this->user = $user;
    $this->apiKey = $apiKey;
  }

  public function authenticate() {
    $response = Request::get(self::AUTH_URL)
      ->addHeader(self::HEADER_USER, $this->user)
      ->addHeader(self::HEADER_KEY, $this->apiKey)
      ->send();

    $statusCode = $response->code;
    if($statusCode<200 || $statusCode>299) throw new \Exception('Error logging in to rackspace cloud');
    foreach($response->headers as $headerName=>$value) {
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



}

