<?php

namespace app\src;

class Error
{

	public function output($code, $message = ''): void
    {
		http_response_code($response_code = $code);
		die('system@error % [' . $response_code . '] ~@ ' . $message . '...');
	}

}