<?php
namespace ClausWitt\Rackspace\Client\Cache;

class Client extends \ClausWitt\Rackspace\Client\Client {


    public function removedCachedObject($bucket, $obj) {
        $endpoint = $bucket . '/' .$obj;
        /** @var $response \Httpful\Response */
        $response = $this->callCdnServerManagement($endpoint);
        return $response;
    }

    public function callCdnServerManagement($endPoint, $additionalHeaders=array()) {

        $apiUrl = $this->getApiUrl($this->cdnManagementUrl, $endPoint);
        echo $apiUrl;
        return $this->callApiDelete($apiUrl, $additionalHeaders);
    }

    private function callApiDelete($url, $additionalHeaders=array()) {
        $request = $this->getRequestObject($url, 'delete');

        foreach($additionalHeaders as $key=>$val) {
            $request->addHeader($key, $val);
        }

        return $request->send();
    }

}