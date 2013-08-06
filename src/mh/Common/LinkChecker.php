<?php

namespace mh\Common;

class LinkChecker
{
	// возвращает false если ни одна ссылка из $links не найдена на странице $url
	// возвоащает массив ключей массива $links ссылки которые присутствуют на странице
	public function hasLinks($url, array $links)
	{

		$content = file_get_contents($url);
		$do=true;
		$urls =array(); //тут соберем все ссылки
		while ($do) {
			$pos = strpos($content,"<a");
			if ($pos === false) $do=false;  //Выходим из цикла
			$content=substr($content,$pos);
			$pos2 = strpos($content,"</a>");
			$urls[] = substr($content,0,$pos2+4);
			$content=substr($content,$pos2+4);
		}

		$pattern = "/^<.*href=\"(http:\/\/[^\"]*)\".*>.*<\/a>$/";
		$answ = array(); // тут буду собирать ответ

		//Теперь пройдемся по каждой ссылке и регуляркой получим адрес сайта!
		foreach ($urls as $subject) {
			preg_match($pattern, $subject, $matches);
			$href=str_replace("www.","",$matches[1]);
			$key = array_search($href,$links);
			if ($key !== false) $answ[$key] = $links[$key];
		}

		if (count($answ) === 0) {
			return false;
		} else {
			return array_keys($answ);
		}
	}
}