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

class HelloAssoIntent {
	public const TABLE = 'intents';
	public const PK = 'id_intent';

	private $zdb;

	public $id;
	public $adherent;
	public $intent_lines;
	public $id_helloasso;
	public $amount;
	public $expires_at;
	public $state;
	public $contains_donation;

	public const STATE_PENDING = 'pending';
	public const STATE_DONE = 'done';
	public const STATE_CANCELLED = 'cancelled';

	public function __construct(Db $zdb)
	{
		$this->zdb = $zdb;
	}

	public function load($id)
	{
		$query = $this->zdb->select(HELLOASSO_PREFIX . '_' . self::TABLE)->where([self:PK, $id]);
		$result = $this->zdb->execute($query);

		$this->id = $result->id_intent;
		$this->adherent = new Adherent($this->zdb, $result->id_adh);
		$this->id_helloasso = $result->id_helloasso;
		$this->amount = $result->amount;
		$this->expires_at = new \DateTimeImmutable($result->expires_at);
		$this->state = $result->state;

		// Load IntentLines
		$query = $this->zdb->select(HELLOASSO_PREFIX . '_' . HelloAssoIntentLine::TABLE)->where(['id_intent' => $this->id]);
		$lines_rs = $this->zdb->execute($query);
		$this->lines = [];
		foreach($lines_as as $line) {
			$new_line = new HelloAssoIntentLine();
			$new_line->type_cotis = ...;
			$new_line->amount = $line->amount;
			$this->lines[] = $new_line;
		}
	}

	public function save()
	{
		$values = [
			'id_adh' => $this->adherent->id,
			'id_type_cotis' => this->type_cotisation->id,
			'id_helloasso' => $this->id_helloasso,
			'amount' => $this->amount,
			'expires_at' => $this->expires_at->format('Y-m-d H:i:s'),
		];
		if(is_null($this->id)) {
			$query = $this->zdb->insert(HELLOASSO_PREFIX . '_' . self::TABLE)->values($values);
			$this->zdb->execute($query);
			$this->id = $this->zdb->getLastGeneratedValue();
		}
		else {
			$query = $this->zdb->update(HELLOASSO_PREFIX . '_' . self::TABLE)->set($values)->where([self::PK, $this->id]);
			$this->zdb->execute($query);
		}

		// Save ItentLines
		$this->zdb->connection->beginTransaction();
		$this->zdb->delete(HELLOASSO_PREFIX . '_' . HelloAssoIntentLine::TABLE)->where(['id_intent', $this->id]);

		$insert_lines = [];
		foreach($this->lines as $line) {
			$insert_lines[] = [
				'id_intent' => $this->id,
				'id_type_cotis' => $line->type_cotis->id,
				'amount' => $line->amount,
		}
		$this->zdb->insert(HELLOASSO_PREFIX . '_' . HelloAssoIntentLine::TABLE)->values($insert_lines);
		$this->zdb->commit();
	}

	public function addLine($type_cotis, $amount)
	{
		$line = new HelloAssoIntentLine();
		$line->type_cotis = $type_cotis;
		$line->amount = $amount;
		$this->amount += $amount;
	}

	public static function cleanupExpired($zdb)
	{
		$query = $zdb->delete(HELLOASSO_PREFIX . '_' . self::TABLE)->where(function ($w) {
			$w->lessThan('expires_at', 'NOW()', $w->TYPE_IDENTIFIER, $w->TYPE_LITERAL);
			$w->equalTo('state', 'pending');
		});
		$zdb->execute($query);
	}
}
