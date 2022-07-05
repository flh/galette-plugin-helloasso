<?php
/**
 * HelloAsso plugin for Galette
 *
 * @author    Florian Hatat
 * @copyright 2022 Florian Hatat
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 */

namespace GaletteHelloAsso;

use Galette\Core\Db;
use Galette\Entity\ContributionsTypes;

class HelloAsso {
	public const TABLE = 'types_cotisation_prices';
	public const PK = ContributionsTypes::PK;
	public const SETTINGS_TABLE = 'settings';

	public const PAYMENT_PENDING = 'Pending';
	public const PAYMENT_COMPLETE = 'Complete';

	public const AUTH_ENDPOINT = 'https://api.helloasso.com/';
	public const TEST_AUTH_ENDPOINT = 'https://api.helloasso-rc.com/';

	private $zdb;

	private $prices = array();
	private $inactives = array();

	private $loaded = false;
	private $amounts_loaded = false;

	private $organisation_slug;
	private $access_token;
	private $refresh_token;
	private $test_mode;

	public function __construct(Db $zdb)
	{
		$this->zdb = $zdb;
		$this->loaded = false;
		$this->prices = array();
		$this->inactives = array();
		$this->id = null;
		$this->load();
	}

	public function load()
	{
		$query = $this->zdb->select(HELLOASSO_PREFIX . self::SETTINGS_TABLE);
		$settings = $this->zdb->execute($query);

		$this->organisation_slug = $settings->org_slug;
		$this->access_token = $settings->access_token;
		$this->refresh_token = $settings->refresh_token;
		$this->loaded = true;
	}

	public function loadAmounts()
	{
		$results = $this->zdb->selectAll(HELLOASSO_PREFIX . self::TABLE);
		foreach($results as $result) {
		}
	}

	/**
	 * Get a valid access token from HelloAsso
	 *
	 * This method may use the refresh token to get a new access token, or
	 * restart the whole authorization process.
	 */
	public function refreshTokens()
	{
		$data = [
			'client_id' => $this->client_id,
			'refresh_token' => $this->refresh_token,
			'grant_type' => 'refresh_token',
		];
	}

	public function getTokens($client_secret)
	{
		$data = [
			'client_id' => $this->client_id,
			'client_secret' => $client_secret,
			'grant_type' => 'client_credentials',
		];
	}

	private function handleTokenResponse($response)
	{
	}
}
