<?php

/**
 * // English
 *
 * HoverText, the display FloatingTextPerticle plugin for PocketMine-MP
 * Copyright (c) 2019-2020  CtrlAltTr0ll < https://github.com/CtrlAltTr0ll >
 *
 * This software is distributed under "NCSA license".
 * You should have received a copy of the NCSA license
 * along with this program.  If not, see
 * < https://opensource.org/licenses/NCSA >.
 *
 * ---------------------------------------------------------------------
 * // 日本語
 *
 * HoverTextはPocketMine-MP向けのFloatingTextPerticleを表示するプラグインです
 * Copyright (c) 2019-2020  CtrlAltTr0ll < https://github.com/CtrlAltTr0ll >
 *
 * このソフトウェアは"NCSAライセンス"下で配布されています。
 * あなたはこのプログラムと共にNCSAライセンスのコピーを受け取ったはずです。
 * 受け取っていない場合、下記のURLからご覧ください。
 * < https://opensource.org/licenses/NCSA >
 */

declare(strict_types = 1);

namespace ctrlalttr0ll\pmmp\HoverText\command\sub;

use pocketmine\Player;
use ctrlalttr0ll\pmmp\HoverText\i18n\Lang;
use ctrlalttr0ll\pmmp\HoverText\i18n\Language;

/**
 * Class HoverTextSubCommand
 * @package ctrlalttr0ll\pmmp\HoverText\command\sub
 */
abstract class HoverTextSubCommand {

  /** @var Player */
  protected $player;
  /** @var Language */
  protected $lang;

  public function __construct(Player $player, string $default = "") {
    $this->player = $player;
    $this->lang = Lang::fromLocale($player->getLocale());
    $this->execute($default);
  }

  abstract public function execute(string $default = ""): void;
}
