<?php
require_once 'vendor/autoload.php';

$gaOption = array(
    'clientId' => '[認証用メルアド]'
  , 'viewId'   => '[ビューID]'
  , 'privateKeyFilePath' => '[秘密鍵のファイルパス]'
);

$notificationOption = array(
    'service' => 'chatwork'
  , 'apiKey'  => '[chatworkのAPIキー]'
  , 'roomId'  => '[ルームID]'
  , 'title'   => '[チャットメッセージのタイトル]'
  , 'to'      => array() //宛先のユーザID（複数指定可能）
);

$template = new \src\models\requestTemplate($gaOption, $notificationOption);
$template->userSummary('[期間]'); //day(日単位),week(週単位),month(月単位)