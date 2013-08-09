<?php

namespace mh\Common;

class LinkChecker
{
	// ���������� false ���� �� ���� ������ �� $links �� ������� �� �������� $url
	// ���������� ������ ������ ������� $links ������ ������� ������������ �� ��������
	public function hasLinks($url, array $links)
	{

		$content = file_get_contents($url);
		$do=true;
		$urls =array(); //��� ������� ��� ������
		while ($do) {
			$pos = strpos($content,"<a");
			if ($pos === false) $do=false;  //������� �� �����
			$content=substr($content,$pos);
			$pos2 = strpos($content,"</a>");
			$urls[] = substr($content,0,$pos2+4);
			$content=substr($content,$pos2+4);
		}

		$pattern = "/^<.*href=\"(http:\/\/[^\"]*)\".*>.*<\/a>$/";
		$answ = array(); // ��� ���� �������� �����

		//������ ��������� �� ������ ������ � ���������� ������� ����� �����!
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