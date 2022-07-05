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
use GaletteHelloAsso\HelloAsso;

class SettingsController extends AbstractPluginController
{
	/**
	 * @Inject("HelloAsso")
	 * @var integer
	 */
	protected $module_info;

	public function settings(Request $request, Response $response) : Response
	{
	}

	public function save(Request $requests, Response $response) : Response
	{
	}
}
