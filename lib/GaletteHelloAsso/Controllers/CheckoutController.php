<?php
/**
 * HelloAsso plugin for Galette
 *
 * @category  Controllers
 * @name      CheckoutController
 * @package   Galette
 * @author    Florian Hatat
 * @copyright 2022 Florian Hatat
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 */

namespace GaletteHelloAsso\Controllers;

use Galette\Controllers\AbstractPluginController;
use Psr\Container\ContainerInterface;
use Slim\Http\Request;
use Slim\Http\Response;
use GaletteHelloAsso\HelloAsso;

class CheckoutController extends AbstractPluginController
{
	/**
	 * @Inject("HelloAsso")
	 * @var integer
	 */
	protected $module_info;

	public function checkout(Request $request, Response $response) : Response
	{
		// FIXME
		$helloAssoIntentUrl = 'https://api.helloasso.com/v5/organizations/{nom-asso}/checkout-intents';

		$request = [
			"totalAmount" => ,
			"initialAmount" => ,
			"itemName" => ,
			"backUrl" => ,
			"errorUrl" => ,
			"returnUrl" => ,
			"containsDonation" => ,
			"payer" => [
				"firstName" => ,
				"lastName" => ,
				"email" => ,
				"dateOfBirth" => ,
				"address" => ,
				"city" => ,
				"zipCode" => ,
				"country" => ,
			],
			"metadata" => [
			],
		];

		// POST checkout intent to HelloAsso
		$intent_response = ...;
		$intent_data = $intent_reponse->getParsedBody();

		return $response->withRedirect($indent_data['redirectUrl']);
	}

	public function payment_return(Request $request, Response $response) : Response
	{
		return $response;
	}

	public function payment_error(Request $request, Response $response) : Response
	{
		return $response;
	}
}
