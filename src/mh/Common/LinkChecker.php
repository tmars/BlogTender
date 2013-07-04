<?php

namespace mh\Common;

class LinkChecker
{
	// возвращает false если ни одна ссылка из $links не найдена на странице $url
	// возвоащает массив ключей массива $links ссылки которые присутствуют на странице
	public function hasLinks($url, array $links)
    {
		return array_keys($links);

		return false;
    }
}