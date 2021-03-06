<?php

namespace mh\Common;

class LikeCountReader
{
	public static function facebook_new($url) {
        // $json_string = file_get_contents('http://graph.facebook.com/?ids='.$url);
        $req = "https://graph.facebook.com/fql?q=select share_count, like_count, comment_count, total_count from link_stat where url=\"".$url."\"";
        $json_string = file_get_contents($req);
        $json = json_decode($json_string, true);
            // print($json);
        if($json){
            foreach($json as $key => $value){
                if(isset($value['shares'])){
                    return intval( @$value['shares']);
                }
                else{
                    return 0;
                }
            }
        }
        else{
            return 0;
        }
    }
    
    public static function facebook($url)
    {
        $facebook_request = @file_get_contents('http://graph.facebook.com/'.$url);
        if (!$facebook_request) {
			return false;
		}
		$fb = json_decode($facebook_request);
        $vb_c = 0;
        if(property_exists($fb,'shares'))
        $vb_c =$fb->shares;
    
        return $vb_c;
    
    }
    
    public static function vk($url)
    {
        //узнать количество лайков на vk
        //необходимо, чтобы был включен модуль ssl в php
        //$id_app = 000000; //id вашего приложения vk
        //echo $vk_request = file_get_contents('http://vk.com/widget_like.php?app='.$id_app.'&url='.$url);
        //$vc_count =  preg_replace('/(.*)var counter = (([0-9])*);(.*)/is','$2',$vk_request );
        //в $vc_count количество лайков в vk
        $vk_request = @file_get_contents('http://vk.com/share.php?act=count&index=1&url='.$url);
		if (!$vk_request) return false;
        $temp = array();
        preg_match('/^VK.Share.count\(1, (\d+)\);$/i',$vk_request,$temp);
        return $temp[1]; //в $temp[1] количество расшариваний, то есть сколько раз нажали "рассказать друзьям"
    
    }
    
    public static function twitter($url)
    {
        //количество твитов в Twitter
        $twitter_request = @file_get_contents('http://urls.api.twitter.com/1/urls/count.json?url='.$url);
		if (!$twitter_request) return false;
        $twitter = json_decode($twitter_request);
        // в $twitter->count число твитов
        return $twitter->count;
    }
    
    public static function odn($url)
    {
        $odnoklassniki_request = @file_get_contents('http://www.odnoklassniki.ru/dk?st.cmd=extOneClickLike&uid=odklocs0&ref='.$url);
		if (!$odnoklassniki_request) return false;
        $temp = array();
        preg_match("/^ODKL.updateCountOC\('[\d\w]+','(\d+)','(\d+)','(\d+)'\);$/i",$odnoklassniki_request,$temp);
        return $temp[1]; //в $temp[1] количество лайков на odnoklassniki.ru
    }
    
    public static function mail($url)
    {
        $mail_request = @file_get_contents('http://connect.mail.ru/share_count?url_list='.$url);
		if (!$mail_request) return false;
        $mail = json_decode($mail_request);
        settype($mail, 'array');
        if(count($mail)>0)
            $mail = $mail[$url];
		settype($mail, 'array');
        if (!$mail) {
			return 0;
		}
		return $mail["shares"]; // в $mail["shares"] и $mail["clicks"] необходимая инфа
    }
    
    public static function gp($url)
    {
            // $google_request = file_get_contents('https://plusone.google.com/u/0/_/+1/fastbutton?count=true&url='.$url);
            // $plusone_count = preg_replace('/(.*)<div id="aggregateCount" class="t1">(([0-9])*)<\/div>(.*)/is','$2',$google_request);
            //return $plusone_count;
			// echo "Google + =".$plusone_count . "<br>";
            //в $plusone_count количество плюсов
        
		$html =  @file_get_contents( "https://plusone.google.com/_/+1/fastbutton?url=".urlencode($url));
		if (!$html) return false;
		$doc = new \DOMDocument();   $doc->loadHTML($html);
		$counter=$doc->getElementById('aggregateCount');
		return $counter->nodeValue;
    }
}