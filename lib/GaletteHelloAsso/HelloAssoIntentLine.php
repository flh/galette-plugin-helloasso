<?php
/**
 * HelloAsso plugin for Galette
 *
 * @author    Florian Hatat
 * @copyright 2022 Florian Hatat
 * @license   http://www.gnu.org/licenses/gpl-3.0.html GPL License 3.0 or (at your option) any later version
 */

namespace GaletteHelloAsso;

class HelloAssoIntentLine {
	public const TABLE = 'intent_line';
	public $type_cotis;
	public $amount;
}
