<?php
/**
 * HelloAsso plugin for Galette
 *
 * @category  Controllers
 * @name      NotificationController
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

class NotificationController extends AbstractPluginController
{
	/**
	 * @Inject("HelloAsso")
	 * @var integer
	 */
	protected $module_info;

	/**
	 * Notification from HelloAsso entry point
	 *
	 * This functions parses notification data and forwards further request
	 * handling to one of the dedicated methods below, according to the
	 * notification type.
	 */
	public function notify(Request $request, Response $response) : Response
	{
		if($request->getMediaType() != 'application/json') {
			// TODO signaler erreur
		}
		$data = $request->getParsedBody();
		if(!is_array($data)) {
			// TODO signaler erreur
		}
		if(!isset($data['eventType'])
			|| !is_string($data['eventType'])
			|| !isset($data['data'])
			|| !is_array($data['data'])
		) {
			// TODO signaler erreur
		}

		if($data['eventType'] === 'Order') {
			return $this->notify_order($request, $response, $data['data']);
		}
		elseif($data['eventType'] === 'Payment') {
			return $this->notify_payment($request, $response, $data['data']);
		}
		elseif($data['eventType'] === 'Form') {
			return $this->notify_form($request, $response, $data['data']);
		}

		// TODO signaler erreur
		return $response;
	}

	/**
	 * Handle an "Order" notification type
	 */
	private function notify_order(Request $request, Response $response, $data) : $Response
	{
	}

	/**
	 * Handle a "Payment" notification type
	 */
	private function notify_payment(Request $request, Response $response, $data) : $Response
	{
	}

	/**
	 * Handle a "Form" notification type
	 */
	private function notify_form(Request $request, Response $response, $data) : $Response
	{
	}
}
