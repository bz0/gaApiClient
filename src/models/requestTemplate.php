<?php
namespace src\models;

class requestTemplate {
    private $client;
    private $template;
    private $notificationOption;
    private $option;
    
    public function __construct($option, $notificationOption){
        $this->option = $option;
        $this->client = new \src\models\gaApiClient(
                $option['clientId']
              , $option['viewId']
              , $option['privateKeyFilePath']
        );
        
        $this->notificationOption = $notificationOption;
        $this->template = new \src\templates\textTemplate();
    }
    
    public function getProfile(){
        $profile = new \src\models\gaProfile(
            $this->option['clientId']
          , $this->option['viewId']
          , $this->client->privateKey
        );
        
        $analytics = $profile->getService();
        $siteInfo  = $profile->getProfile($analytics);
        
        return $siteInfo;
    }
    
    public function date($period){
        $dateCalc = new \src\models\dateCalc();
        
        switch($period){
            case 'month':
                $toMonth   = date('Y-m-1');
                $to    = $dateCalc->month($toMonth, 1);
                $last  = $dateCalc->month($toMonth, 2);
                break;
            case 'week':
                $now   = date('Y-m-d');
                $to    = $dateCalc->week($now, 1);
                $last  = $dateCalc->week($now, 2);
                break;
            case 'yesterday':
                $now   = date('Y-m-d');
                $to    = $dateCalc->date($now, 1);
                $last  = $dateCalc->date($now, 2);
                break;
        }
        
        $res = array(
            'to'   => $to
          , 'last' => $last
        );
        
        return $res;
    }
    
    public function request($from, $to, $metrics, $option){
        $res = $this->client->request($from, $to, $metrics, $option);
        return $res;
    }
    
    public function convertArray($obj){
        $json  = json_encode($obj);
        $array = json_decode($json, true);
        
        return $array;
    }
    
    public function diff($to, $last){
        $diff = array();
        foreach($to as $key => $val){
            $diff[$key] = $to[$key] - $last[$key];
            if ($diff[$key]>0){
                $diff[$key] = "+" . $diff[$key];
            }
        }
        
        return $diff;
    }
    
    public function userSummary($period){
        $date = $this->date($period);
        $metrics    = 'ga:sessions , ga:users , ga:pageviews , ga:pageviewsPerSession , ga:avgSessionDuration , ga:bounceRate , ga:percentNewSessions';
        $dimensions = 'ga:pageTitle, ga:pagePath';
        
        $option = array(
            'dimensions' => $dimensions ,
            'max-results' => 10,
            'sort' => '-ga:pageviews'
        );
        
        $to   = $this->request($date['to']['start'], $date['to']['end'], $metrics, $option);
        $last = $this->request($date['last']['start'], $date['last']['end'], $metrics, $option);
        
        $to   = $this->convertArray($to);
        $last = $this->convertArray($last);
        
        $diff = $this->diff(
            $to['totalsForAllResults']
          , $last['totalsForAllResults']
        );
        
        $profile = $this->getProfile();
        
        $params = array(
            'period' => $date['to']
          , 'to'     => $to['totalsForAllResults']
          , 'diff'   => $diff
          , 'pageRanking' => $to['rows']
          , 'profile' => $profile
        );
        
        $text = $this->template->userSummary(
            $params
          , $period
        );
        
        $this->notificationOption['text'] = $text;
        
        $this->notification($this->notificationOption);
        
        return $text;
    }
    
    public function notification(){
        $notification = new \src\models\notification();
        
        if ($this->notificationOption['service']=='chatwork'){
            $notification->chatwork($this->notificationOption);
        }
    }
}
