<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class WelcomeController extends AbstractController
{
	public function welcomeAction()
	{
		$version = \App\Kernel::VERSION;
		$baseDir = realpath('..').DIRECTORY_SEPARATOR;

		ob_start();
		include $baseDir . 'templates/welcome.html.php';

		return new Response(ob_get_clean());
	}
}
