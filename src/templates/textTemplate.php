<?php
namespace src\templates;

class textTemplate {
    public function userSummary($params, $period){
        $diffTarget = '';
        switch($period){
            case 'date':
                $diffTarget = '前日';
                break;
            case 'week':
                $diffTarget = '前週';
                break;
            case 'month':
                $diffTarget = '前月';
                break;
        }
        
        $text = <<<TEXT
サイト名：{$params['profile']['name']} {$params['profile']['websiteUrl']}
集計期間：{$params['period']['start']} ～ {$params['period']['end']}

■ユーザーサマリー：
セッション：{$params['to']['ga:sessions']} ({$diffTarget}対比：{$params['diff']['ga:sessions']})
ユーザ：{$params['to']['ga:users']} ({$diffTarget}対比：{$params['diff']['ga:users']})
ページビュー数：{$params['to']['ga:pageviews']} ({$diffTarget}対比：{$params['diff']['ga:pageviews']})
ページ/セッション：{$params['to']['ga:pageviewsPerSession']} ({$diffTarget}対比：{$params['diff']['ga:pageviewsPerSession']})
平均セッション時間：{$params['to']['ga:avgSessionDuration']} 秒 ({$diffTarget}対比：{$params['diff']['ga:avgSessionDuration']} 秒)
直帰率：{$params['to']['ga:bounceRate']} % ({$diffTarget}対比：{$params['diff']['ga:bounceRate']} %)
新規セッション率：{$params['to']['ga:percentNewSessions']} % ({$diffTarget}対比：{$params['diff']['ga:percentNewSessions']} %)
TEXT;
        $rankText = '';
        foreach($params['pageRanking'] as $cnt => $page){
            if ($rankText!=''){
                $rankText .= "\n\n";
            }
            
            $rank = $cnt + 1;
            $rankText .= $rank . "位 " . $page[0] . " " . $page[4] . " pv\n";
            
            $path = preg_replace('{^/(.*?)}', '$1', $page[1]);
            $rankText .= $params['profile']['websiteUrl'] . $path;
        }
        
        if ($rankText!=''){
            $rankText = <<<TEXT


■ページビューランキング：
{$rankText}
TEXT;
        }
        
        $text = $text . $rankText;
        
        return $text;
    }
    
    public function searchQuery($to, $last){
        $text = <<<TEXT

TEXT;
    }
    
    public function pageRanking($period){
        $text = <<<TEXT

TEXT;
    }
}
