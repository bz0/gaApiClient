<?php
namespace src\models;

class gaProfile{
    public $clientId;
    public $privateKey;
    public $viewId;

    public function __construct($clientId, $viewId, $privateKey){
        $this->clientId   = $clientId;
        $this->viewId     = $viewId;
        $this->privateKey = $privateKey;
    }

    private function privateKeyRead($privateKeyFilePath){
        $this->privateKey = @file_get_contents($privateKeyFilePath);
    }

    public function getService(){
        $client = new \Google_Client();
        $client->setApplicationName("MyAnalyticsApp");
        $analytics = new \Google_Service_Analytics($client);

        $cred = new \Google_Auth_AssertionCredentials(
            $this->clientId,
            array(\Google_Service_Analytics::ANALYTICS_READONLY),
            $this->privateKey
        );

        $client->setAssertionCredentials($cred);
        if($client->getAuth()->isAccessTokenExpired()) {
          $client->getAuth()->refreshTokenWithAssertion($cred);
        }

        return $analytics;
    }

    public function getProfile($analytics) {
        $accounts = $analytics->management_accounts->listManagementAccounts();
        $res      = array();

        if (count($accounts->getItems())>0) {
            $items = $accounts->getItems();
            
            print_r($items);

            foreach($items as $i => $item){
                $accountId = $item->getId();
                $profiles = $analytics->management_webproperties
                      ->listManagementWebproperties($accountId);

                if (count($profiles->getItems()) > 0) {
                    $item = $profiles->getItems();
                    
                    $json = json_encode($item);
                    $array = json_decode($json, true);
                    
                    echo $array[0]['defaultProfileId'] . " | " . $this->viewId . "<br>";
                    if ($array[0]['defaultProfileId']==$this->viewId){
                        $res = $array[0];
                    }
                }
            }
        }
        
        return $res;
    }
}