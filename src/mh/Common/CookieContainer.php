<?php

namespace mh\Common;

class CookieContainer
{
	private $cookies = array();

	public function __construct($request)
	{
		$cookies = $request->cookies->all();
        foreach ($cookies as $key => $value) {
        	if (preg_match('/_a$/', $key)) {
            	$value = explode(',', $value);
            }
            $this->cookies[$key] = $value;
        }

        $this->updated = true;
	}

    protected function in($key, $val)
    {
    	if (isset($this->cookies[$key]) &&
        	in_array($val, $this->cookies[$key])) {
        	return true;
        }

        return false;
    }

    public function get($key, $val = '')
    {
        if (isset($this->cookies[$key])) return $this->cookies[$key];
        return $val;
    }

    public function set($key, $val)
    {
    	$this->cookies[$key] = $val;
    }

    public function delCookie($key, $val)
    {
    	if (isset($this->cookies[$key])) {
        	foreach ($this->cookies[$key] as $k => $v) {
           		if ($v == $val) {
                	unset($this->cookies[$key][$k]);
            		break;
                }
            }
        }
    }

    public function addCookie($key, $val)
    {
    	if (!isset($this->cookies[$key])) {
    		$this->cookies[$key] = array();
        }
        $this->cookies[$key][] = $val;
    }

	public function getData()
	{
		$data = array();
		foreach ($this->cookies as $key => $value) {
			if (is_array($value)) {
            	$value = implode(',', $value);
            }
			$data[$key] = $value;
		}
		return $data;
	}
}