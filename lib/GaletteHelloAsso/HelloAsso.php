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
use Laminas\Http\Client as HttpClient;
use Laminas\Http\Request as HttpRequest;

class HelloAsso {
	public const TABLE = 'types_cotisation_prices';
	public const PK = ContributionsTypes::PK;
	public const SETTINGS_TABLE = 'settings';

	public const PAYMENT_PENDING = 'Pending';
	public const PAYMENT_COMPLETE = 'Complete';

	public const API_ENDPOINT = 'https://api.helloasso.com/';
	public const TEST_API_ENDPOINT = 'https://api.helloasso-rc.com/';

	private $zdb;

	private $amounts = array();

	private $loaded = false;
	private $amounts_loaded = false;

	private $organisation_slug;
	private $access_token;
	private $refresh_token;
	private $access_expires_at;
	private $refresh_expires_at;
	private $test_mode;

	public function __construct(Db $zdb)
	{
		$this->zdb = $zdb;
		$this->loaded = false;
		$this->prices = array();
		$this->inactives = array();
		$this->load();
	}

	public function load()
	{
		$query = $this->zdb->select(HELLOASSO_PREFIX . self::SETTINGS_TABLE);
		$settings = $this->zdb->execute($query);

		$this->organisation_slug = $settings->org_slug;
		$this->access_token = $settings->access_token;
		$this->refresh_token = $settings->refresh_token;
		$this->access_expires_at = \DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $settings->access_expires_at);
		$this->refresh_expires_at = \DateTimeImmutable::createFromFormat("Y-m-d H:i:s", $settings->refresh_expires_at);
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
	public function getAccessToken()
	{
		if($this->accessTokenExpiresSoon()) {
			if($this->refreshTokenExpiresSoon()) {
				$this->getTokens();
			}
			else {
				$this->refreshTokens();
			}
		}
		return $this->access_token;
	}

	private function accessTokenExpiresSoon()
	{
		return $this->access_expires_at > new \DateTimeImmutable("1 minute ago");
	}

	private function refreshTokenExpired()
	{
		return $this->refresh_expires_at > new \DateTimeImmutable("1 day ago");
	}

	private function refreshTokenExpiresSoon()
	{
		return $this->refresh_expires_at > new \DateTimeImmutable("10 days ago");
	}

	private function refreshTokens()
	{
		return $this->retrieveTokens([
			'client_id' => $this->client_id,
			'refresh_token' => $this->refresh_token,
			'grant_type' => 'refresh_token',
		]);
	}

	private function getTokens()
	{
		return $this->retrieveTokens([
			'client_id' => $this->client_id,
			'client_secret' => $this->client_secret,
			'grant_type' => 'client_credentials',
		]);
	}

	private function retrieveTokens($params)
	{
		$client = new HttpClient(
			($this->test_mode ? self::TEST_API_ENDPOINT : self::API_ENDPOINT) . 'oauth2/token',
			[ 'adapter' => 'Laminas\Http\Client\Adapter\Curl' ]
		);
		$client->setMethod(HttpRequest::METHOD_POST);
		$client->setParameterPost($params);

		$now = new \DateTimeImmutable();
		$response = $client->send();

		if(!$response->isSuccess()) {
			return null;
		}

		$json = $response->getParsedBody();

		$this->access_token = $json['access_token'];
		$this->refresh_token = $json['refresh_token'];
		$this->access_expires_at = $now->add(new \DateInterval("PT" . $json['expires_in'] . "S"));
		// Hard-coded duration given by API documentation (says refresh
		// token expires after one month).
		$this->refresh_expires_at = $now->add(new \DateInterval("PT30D"));
		$this->saveTokens();

		return $this->access_token;
	}

	private function saveTokens()
	{
		$query = $this->zdb->update(HELLOASSO_PREFIX . self::SETTINGS_TABLE)->set([
			'access_token' => $this->access_token,
			'refresh_token' => $this->refresh_token,
			'access_expires_at' => $this->access_expires_at->format('Y-m-d H:i:s'),
			'refresh_expires_at' => $this->refresh_expires_at->format('Y-m-d H:i:s'),
		]);
		$this->zdb->execute($query);
	}

	private function saveSettings()
	{
		$query = $this->zdb->update(HELLOASSO_PREFIX . self::SETTINGS_TABLE)->set([
			'org_slug' => $this->organization_slug,
			'client_id' => $this->client_id,
			'client_secret' => $this->client_secret,
			'test_mode' => $this->test_mode ? 1 : 0,
		]);
		$this->zdb->execute($query);
	}

	public function getIntentUrl()
	{
		return ($this->test_mode ? self::TEST_API_ENDPOINT : self::API_ENDPOINT) . "v5/organizations/{$this->organization_slug}/checkout-intents";
	}
}
