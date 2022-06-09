<?php

namespace test;

use \app\src\Error;

class Filename
{
	
	public function check(): void
    {
		(new Error())->output(200, 'Ok');
	}

}