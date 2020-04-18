<?php
/**
 * Created by PhpStorm.
 * User: User
 * Date: 09.08.2018
 * Time: 13:28
 */

namespace Corp\Libraries;

use Illuminate\Http\Request;
use Illuminate\Routing\RouteCollection;
use Illuminate\Routing\UrlGenerator;
class CustomUrlGenerator extends UrlGenerator
{
	public function __construct(RouteCollection $routes, Request $request)
	{
		parent::__construct($routes, $request);
	}
	/**
	 * {@inheritdoc}
	 */

	public function route( $name, $parameters = [], $absolute = true ) {

		if(isset($_GET['lr_preview_customize']) &&  $_GET['lr_preview_customize'] == true) {
			$parameters['lr_preview_customize'] = true;
		}

		return parent::route( $name, $parameters, $absolute ) ; // TODO: Change the autogenerated stub
	}
}