<?php
namespace src\models;

class gaApiClient {
    public $clientId;
    public $viewId;
    public $privateKey;
    public $client;
    
    public function __construct($clientId, $viewId, $privateKeyFilePath){
        $this->clientId = $clientId;
        $this->viewId   = $viewId;
	$this->privateKeyRead($privateKeyFilePath);
    }
    
    private function privateKeyRead($privateKeyFilePath){
	$this->privateKey = @file_get_contents($privateKeyFilePath);
    }
    
    private function setToken(){
	// トークンのセット
	if(isset($_SESSION['service_token'])){
            $this->client->setAccessToken($_SESSION['service_token']);
	}
    }
    
    /*
     * 資格情報の生成
     */
    private function credentials(){
	// スコープのセット (読み込みオンリー)
        $url = 'https://www.googleapis.com/auth/analytics.readonly';
	$scopes = array($url);
	// クレデンシャルの作成
	$credentials = new \Google_Auth_AssertionCredentials(
                $this->clientId
              , $scopes
              , $this->privateKey ) ;
	// Googleクライアントのインスタンスを作成
	$this->client = new \Google_Client() ;
	$this->client->setAssertionCredentials($credentials);
        
        return $credentials;
    }
    
    private function refreshToken($credentials){
	// トークンのリフレッシュ
	if($this->client->getAuth()->isAccessTokenExpired()){
            $this->client->getAuth()->refreshTokenWithAssertion( $credentials ) ;
	}

	$_SESSION['service_token'] = $this->client->getAccessToken();
    }

    private function fetch($from, $to, $metrics, $option){
	$analytics = new \Google_Service_Analytics($this->client);
	$res = $analytics->data_ga->get(
                'ga:' . $this->viewId
              , $from 
              , $to 
              , $metrics 
              , $option
        );
        
        return $res;
    }

    public function request($from, $to, $metrics, $option){
        $this->setToken();
        $credentials = $this->credentials();
        $this->refreshToken($credentials);
        $res = $this->fetch($from, $to, $metrics, $option);
        
        return $res;
    }
}
