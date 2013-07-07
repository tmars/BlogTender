<?php

namespace mh\BTBundle\Controller\Frontend\Base;

class SocnetLoginController extends BaseUserController
{
	protected $params = array(
		'vk' => array(
			'get_data_url_pattern' => 'https://api.vk.com/method/users.get?uid={user_id}&fields=uid,home_phone,first_name,last_name,nickname,screen_name,sex,bdate,city,country,timezone,email,photo_big&access_token={access_token}',
			'get_access_token_url' => 'https://oauth.vk.com/access_token',
			'default_foto_url' => 'https://vk.com/images/camera_a.gif',
		),
		'fb' => array(
			'get_data_url_pattern' => 'https://graph.facebook.com/me?access_token={access_token}&fields=picture,username,name,email,id,timezone,gender',
			'get_access_token_url' => 'https://graph.facebook.com/oauth/access_token',
		),
		'gl' => array(
			'get_data_url_pattern' => 'https://www.googleapis.com/oauth2/v1/userinfo?access_token={access_token}',
			'get_access_token_url' => 'https://accounts.google.com/o/oauth2/token',
		),
		'od' => array(
			'get_data_url_pattern' => 'http://api.odnoklassniki.ru/fb.do?method=users.getCurrentUser&access_token={access_token}&application_key={public_key}&sig={sign}',
			'get_access_token_url' => 'http://api.odnoklassniki.ru/oauth/token.do',
		),
		'ml' => array(
			'get_data_url_pattern' => 'http://www.appsmail.ru/platform/api?method=users.getInfo&secure=1&app_id={client_id}&session_key={access_token}&sig={sign}',
			'get_access_token_url' => 'https://connect.mail.ru/oauth/token',
			'sign_pattern' => 'app_id={client_id}method=users.getInfosecure=1session_key={access_token}{secret_key}',
		),
	);
	protected $mode;

	protected function initMode($mode)
	{
		$this->mode = $mode;
		$modes = array('gl', 'vk', 'fb', 'ml', 'od');
		foreach ($modes as $m) {
			$this->params[$m]['app_id'] = $this->container->getParameter($m.'_app_id');
			$this->params[$m]['secret_key'] = $this->container->getParameter($m.'_secret_key');
		}
		$this->params['od']['public_key'] = $this->container->getParameter('od_public_key');
	}

	protected function getP($key)
	{
		return $this->params[$this->mode][$key];
	}

	protected function getRedirectUri()
	{
		return $this->generateUrl('profile_socnet_login', array('mode' => $this->mode), true);
	}

	protected function getUserDataFrom($mode)
	{
		$this->initMode($mode);
		$userData = null;

		if ($mode == 'vk') {
			$content = $this->sendGetRequest($this->getP('get_access_token_url'), array(
				'client_id' => $this->getP('app_id'),
				'redirect_uri' => $this->getRedirectUri(),
				'client_secret' => $this->getP('secret_key'),
				'code' => $this->getRequest()->get('code')
			));
			if (!$content) return null;
			$data = $this->unpackData($content);

			$content = $this->sendGetRequest($this->renderUrl($this->getP('get_data_url_pattern'), $data));
			if (!$content) return null;
			$data = $this->unpackData($content);

			$data = $data['response'][0];
			$userData['id'] = $data['uid'];
			$userData['screen_name'] = $data['screen_name'];
			$userData['photo'] = $data['photo_big'] == $this->getP('default_foto_url') ? '' : $data['photo_big'];
			$userData['name'] = sprintf("%s %s", $data['first_name'], $data['last_name']);
			$userData['socnet_info'] = $content;

		} else if ($mode == 'fb') {

			$content = $this->sendGetRequest($this->getP('get_access_token_url'), array(
				'client_id' => $this->getP('app_id'),
				'redirect_uri' => $this->getRedirectUri(),
				'client_secret' => $this->getP('secret_key'),
				'code' => $this->getRequest()->get('code')
			));
			if (!$content) return null;
			$data = $this->unpackData($content);

			$content = $this->sendGetRequest($this->renderUrl($this->getP('get_data_url_pattern'), $data));
			if (!$content) return null;
			$data = $this->unpackData($content);

			$userData['id'] = $data['id'];
			$userData['screen_name'] = $data['username'];
			$userData['name'] = $data['name'];
			$userData['email'] = $data['email'];
			$userData['photo'] = $data['picture']['data']['is_silhouette'] == true ? '' : $data['picture']['data']['url'];
			$userData['socnet_info'] = $content;
		} else if ($mode == 'od') {

			$content = $this->sendPostRequest($this->getP('get_access_token_url'), array(
				'code' => $this->getRequest()->get('code'),
				'redirect_uri' => $this->getRedirectUri(),
				'grant_type' => 'authorization_code',
				'client_id' => $this->getP('app_id'),
				'client_secret' => $this->getP('secret_key'),
			));
			if (!$content) return null;
			$data = $this->unpackData($content);

			$data['public_key'] = $this->getP('public_key');
			$data['sign'] = md5(
				'application_key='.$this->getP('public_key').'method=users.getCurrentUser'.md5($data['access_token'].$this->getP('secret_key'))
			);
			$content = $this->sendGetRequest($this->renderUrl($this->getP('get_data_url_pattern'), $data));
			if (!$content) return null;
			$data = $this->unpackData($content);
			
			$userData['id'] = $data['uid'];
			$userData['screen_name'] = $data['name'];
			$userData['name'] = $data['name'];
			$userData['photo'] = $data['pic_2'];
			$userData['socnet_info'] = $content;

		} else if ($mode =='gl') {
			$content = $this->sendPostRequest($this->getP('get_access_token_url'), array(
				'client_id' => $this->getP('app_id'),
				'client_secret' => $this->getP('secret_key'),
				'redirect_uri' => $this->getRedirectUri(),
				'grant_type' => 'authorization_code',
				'code' => $this->getRequest()->get('code')
			));
			if (!$content) return null;
			$data = $this->unpackData($content);

			$content = $this->sendGetRequest($this->renderUrl($this->getP('get_data_url_pattern'), $data));
			if (!$content) return null;
			$data = $this->unpackData($content);

			$userData['id'] = $data['id'];
			$userData['screen_name'] = $data['verified_email'] == true ? substr($data['email'], 0, strpos($data['email'], '@')) : $data['name'];
			$userData['name'] = $data['name'];
			$userData['email'] = $data['verified_email'] == true ? $data['email'] : '';
			$userData['photo'] = $data['picture'] ? $data['picture'] : '';
			$userData['socnet_info'] = $content;

		} else if ($mode =='ml') {
			$content = $this->sendPostRequest($this->getP('get_access_token_url'), array(
				'client_id' => $this->getP('app_id'),
				'client_secret' => $this->getP('secret_key'),
				'redirect_uri' => $this->getRedirectUri(),
				'grant_type' => 'authorization_code',
				'code' => $this->getRequest()->get('code')
			));
			if (!$content) return null;
			$data = $this->unpackData($content);
			$data['secret_key'] = $this->getP('secret_key');
			$data['client_id'] = $this->getP('app_id');
			$data['sign'] = md5($this->renderUrl($this->getP('sign_pattern'), $data));

			$content = $this->sendGetRequest($this->renderUrl($this->getP('get_data_url_pattern'), $data));
			if (!$content) return null;
			$data = $this->unpackData($content);

			$data = $data[0];
			$userData['id'] = $data['uid'];
			$userData['screen_name'] = substr($data['email'], 0, strpos($data['email'], '@'));
			$userData['name'] = $data['nick'];
			$userData['email'] = $data['email'];
			$userData['photo'] = $data['has_pic'] ? $data['pic_190'] : '';
			$userData['socnet_info'] = $content;

		} else if ($mode == 'tw') {
			session_start();

			if(empty($_GET['oauth_verifier']) || empty($_SESSION['oauth_token']) || empty($_SESSION['oauth_token_secret'])){
				return null;
			}

			try {
				$twitteroauth = $this->get('twitter_oauth');
				$twitteroauth->consume($_SESSION['oauth_token'], $_SESSION['oauth_token_secret']);

				$access_token = $twitteroauth->getAccessToken($_GET['oauth_verifier']);

				$_SESSION['access_token'] = $access_token;

				$data = $twitteroauth->get('account/verify_credentials');
			} catch (\Exception $e) {
				return null;
			}

			$userData['id'] = $data->id;
			$userData['screen_name'] = $data->screen_name;
			$userData['name'] = $data->name;
			$userData['photo'] = $data->profile_image_url;
			$userData['socnet_info'] = var_dump($data);
		}

		return $userData;
	}

	protected function matchKeys($key_matches, $data)
	{
		$result = array();
		foreach ($key_matches as $result_key => $path) {
			if ($path) {
				$keys = explode('.', $path);
				$val = $data;
				while ($keys) {
					$key = array_shift($keys);
					if (array_key_exists($key, $val)) {
						$val = $val[$key];
					} else {
						return false;
					}
				};
			} else {
				$val = '';
			}
			$result[$result_key] = $val;
		}
		return $result;
	}

	protected function unpackData($content)
	{
		$data = json_decode($content, true);
		if (! $data) {
			parse_str($content, $data);
		}

		return $data;
	}

	protected function renderUrl($pattern, $parameters)
	{
		foreach ($parameters as $key => $value) {
			$pattern =  preg_replace('/\{'.$key.'\}/', $value, $pattern);
		}
		return $pattern;
	}

	protected function sendPostRequest($url, $data)
	{
		//инициализируем сеанс
		$curl = curl_init();

		//уcтанавливаем урл, к которому обратимся
		curl_setopt($curl, CURLOPT_URL, $url);

		//включаем вывод заголовков
		//curl_setopt($curl, CURLOPT_HEADER, 1);

		//передаем данные по методу post
		curl_setopt($curl, CURLOPT_POST, 1);

		//теперь curl вернет нам ответ, а не выведет
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);

		//переменные, которые будут переданные по методу post
		curl_setopt($curl, CURLOPT_POSTFIELDS, http_build_query($data));

		//я не скрипт, я браузер опера
		curl_setopt($curl, CURLOPT_USERAGENT, 'Opera 10.00');

		$res = curl_exec($curl);

		curl_close($curl);

		//проверяем, если ошибка, то получаем номер и сообщение
		if (!$res){
			//$error = curl_error($curl).'('.curl_errno($curl).')';
			//echo $error;
			return false;
		} else {
		//если не ошибка, то выводим результат
			return $res;
		}

	}

	protected function sendGetRequest($url, $data = array())
	{
		if ($data) {
			$url = $url . '?' . http_build_query($data);
		}
		$r = file_get_contents($url);
		if (!strpos($http_response_header[0], "200")) {
			$r = false;
		}
		return $r;
	}
}
