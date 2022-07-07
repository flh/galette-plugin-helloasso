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
use GaletteHelloAsso\HelloAssoIntent;
use Laminas\Http\Client as HttpClient;
use Laminas\Http\Request as HttpRequest;

class CheckoutController extends AbstractPluginController
{
	/**
	 * @Inject("HelloAsso")
	 * @var integer
	 */
	protected $module_info;

	public function checkout_form(Request $request, Response $response) : Response
	{
	}

	public function checkout(Request $request, Response $response) : Response
	{
		$post = $request->getBody();

		$helloasso = $this->container->get(HelloAsso::class);
		$helloasso->loadAmounts();
		$intent = new HelloAssoIntent($this->zdb);
		$intent->adherent = $this->login;

		for($i = 0; $i < intval($post['intent-line-count'] ?? 0) && $i < 30; $i++) {
			$id_cotis = $post["intent-line-$i-type-cotis"] ?? null;
			$amount = $post["intent-line-$i-amount"] ?? null;
			if(!is_numeric($id_cotis) || !is_numeric($amount)) {
				// TODO signaler erreur
				continue;
			}
			$id_cotis = intval($id_cotis);
			$amount = intval(100 * floatval($amount));
			if(!array_key_exists($id_cotis, $helloasso->amounts)) {
				// TODO signaler erreur
				continue;
			}
			if(!$helloasso->amounts[$id_cotis]->can_change_amount
				&& $amount != intval(100 * $helloasso->amounts[$id_cotis]->amount)) {
				// TODO signaler erreur
				continue;
			}
			// TODO changer en type_cotis
			$intent->addLine($type_cotis, $amount / 100);
		}

		$intent->expires_at = new \DateTimeImmutable("15 minutes");
		$intent->state = HelloAssoIntent::STATE_PENDING;

		$params = [
			"totalAmount" => intval($intent->amount),
			"initialAmount" => intval($intent->amount),
			"itemName" => ...,
			"backUrl" => $this->router->pathFor('helloasso_payment_cancel'),
			"errorUrl" => $this->router->pathFor('helloasso_payment_error'),
			"returnUrl" => $this->router->pathFor('helloasso_payment_return'),
			"containsDonation" => $intent->contains_donation,
			"payer" => [
				"firstName" => $this->login->surname,
				"lastName" => $this->login->name,
				"email" => $this->login->email,
				"address" => $this->login->getAddress(),
				"city" => $this->login->getTown(),
				"zipCode" => $this->login->getZipcode(),
				"country" => $this->login->getCountry(),
			],
			"metadata" => [
			],
		];

		// POST checkout intent to HelloAsso
		$client = new HttpClient($helloasso->getIntentUrl(),
			[ 'adapter' => 'Laminas\Http\Client\Adapter\Curl' ]
		);
		$client->setMethod(HttpRequest::METHOD_POST);
		$client->setHeaders([
			'Content-type' => 'application/json',
			'Authorization' => 'Bearer ' . $helloasso->getAccessToken(),
		]);
		$client->getRequest()->setContent(\json_encode($params));
		$intent_response = $client->send();
		$intent_data = $intent_reponse->getParsedBody();
		//TODO is error?

		// Store checkout intent in database
		$intent->id_helloasso = $intent_data['id'];
		$intent->save();

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

	public function payment_cancel(Request $request, Response $response) : Response
	{
		return $response;
	}
}
