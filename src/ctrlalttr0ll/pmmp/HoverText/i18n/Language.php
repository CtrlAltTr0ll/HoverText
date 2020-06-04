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

namespace ctrlalttr0ll\pmmp\HoverText\i18n;

use pocketmine\lang\BaseLang;
use ctrlalttr0ll\pmmp\HoverText\Core;

/**
 * Class Language - simple wrapper for BaseLang
 * @package ctrlalttr0ll\pmmp\HoverText\language
 */
class Language extends BaseLang {

  public function __construct(string $lang) {
    $path = Core::get()->getDataFolder().Lang::DIR.DIRECTORY_SEPARATOR;
    parent::__construct($lang, $path, Lang::FALLBACK);
  }
}