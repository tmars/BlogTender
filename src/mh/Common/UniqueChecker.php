<?php

namespace mh\Common;

class UniqueChecker
{
	public function isUnique($content)
    {
		if ($content == '123') {
			return false;
		}

		return true;
    }
}