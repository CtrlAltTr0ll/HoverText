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

namespace ctrlalttr0ll\pmmp\HoverText;

use pocketmine\level\Level;
use pocketmine\Server;
use ctrlalttr0ll\pmmp\HoverText\data\FloatingTextData;
use ctrlalttr0ll\pmmp\HoverText\text\FloatingText;
use ctrlalttr0ll\pmmp\HoverText\text\Text;
use ctrlalttr0ll\pmmp\HoverText\text\UnremovableFloatingText;

/**
 * Class HoverTextApi
 * @package ctrlalttr0ll\pmmp\HoverText
 */
class HoverTextApi {

  /** @var array */
  private static $ufts = [];
  /** @var array */
  private static $fts = [];

  private function __construct() {
    // DO NOT USE THIS METHOD!
  }

  /**
   * Register text in the HoverText plugin and enable management using HoverTextApi
   * If you don't do this registration,
   * you need to manage FloatingText manually with the Text class.
   * @link ctrlalttr0ll\pmmp\HoverText\text\{Text, FloatingText}
   * @param Text $text
   */
  public static function registerText(Text $text): void {
    switch (true) {
      case $text instanceof UnremovableFloatingText:
        self::$ufts[$text->getLevel()->getFolderName()][$text->getName()] = $text;
        break;

      case $text instanceof FloatingText:
        self::$fts[$text->getLevel()->getFolderName()][$text->getName()] = $text;
        FloatingTextData::make()->saveFtChange($text);
        break;
    }
  }

  /**
   * @return array[string FolderName][string TextName] = UnremovableFloatingText
   */
  public static function getUfts(): array {
    return self::$ufts;
  }

  /**
   * @param Level $level
   * @return array[string FolderName][string TextName] = UnremovableFloatingText
   */
  public static function getUftsByLevel(Level $level): array {
    return self::getUftsByLevelName($level->getFolderName());
  }

  /**
   * @param string $levelName
   * @return array[string FolderName][string TextName] = UnremovableFloatingText
   */
  public static function getUftsByLevelName(string $levelName): array {
    return self::$ufts[$levelName] ?? [];
  }

  /**
   * @param Level $level
   * @param string $name
   * @return null|UnremovableFloatingText
   */
  public static function getUftByLevel(Level $level, string $name): ?UnremovableFloatingText {
    $ufts = self::getUftsByLevel($level);
    return $ufts[$name] ?? null;
  }

  /**
   * @param string $levelName
   * @param string $name
   * @return null|UnremovableFloatingText
   */
  public static function getUftByLevelName(string $levelName, string $name): ?UnremovableFloatingText {
    $ufts = self::getUftsByLevelName($levelName);
    return $ufts[$name] ?? null;
  }

  /**
   * @return array[string FolderName][string TextName] = FloatingText
   */
  public static function getFts(): array {
    return self::$fts;
  }

  /**
   * @param Level $level
   * @return array[string FolderName][string TextName] = FloatingText
   */
  public static function getFtsByLevel(Level $level): array {
    return self::getFtsByLevelName($level->getFolderName());
  }

  /**
   * @param string $levelName
   * @return array[string FolderName][string TextName] = FloatingText
   */
  public static function getFtsByLevelName(string $levelName): array {
    return self::$fts[$levelName] ?? [];
  }

  /**
   * @param Level $level
   * @param string $name
   * @return null|FloatingText
   */
  public static function getFtByLevel(Level $level, string $name): ?FloatingText {
    $fts = self::getFtsByLevel($level);
    return $fts[$name] ?? null;
  }

  /**
   * @param string $levelName
   * @param string $name
   * @return null|FloatingText
   */
  public static function getFtByLevelName(string $levelName, string $name): ?FloatingText {
    $fts = self::getFtsByLevelName($levelName);
    return $fts[$name] ?? null;
  }

  /**
   * @param Level $level
   * @return bool
   */
  public static function removeFtsByLevel(Level $level): bool {
    $fts = self::getFtsByLevel($level);
    $onLevel = $fts[$level->getFolderName()] ?? [];
    if (!empty($onLevel)) {
      /** @var FloatingText $ft */
      foreach ($onLevel as $ft) {
        $ft->sendToLevel($level, Text::SEND_TYPE_REMOVE);
      }
      FloatingTextData::make()->removeFtsByLevel($level);
      unset(self::$fts[$level->getFolderName()]);
      return true;
    }
    return false;
  }

  /**
   * @param string $levelName
   * @return bool
   */
  public static function removeFtsByLevelName(string $levelName): bool {
    $level = Server::getInstance()->getLevelByName($levelName);
    return isset($level) ? self::removeFtsByLevel($level) : false;
  }

  /**
   * @param Level $level
   * @param string $name
   * @return bool
   */
  public static function removeFtByLevel(Level $level, string $name): bool {
    $ft = self::getFtByLevel($level, $name);
    if (isset($ft)) {
      $ft->sendToLevel($level, Text::SEND_TYPE_REMOVE);
      FloatingTextData::make()->removeFtByLevel($level, $name);
      unset(self::$fts[$level->getFolderName()][$name]);
      return true;
    }
    return false;
  }

  /**
   * @param string $levelName
   * @param string $name
   * @return bool
   */
  public static function removeFtByLevelName(string $levelName, string $name): bool {
    $level = Server::getInstance()->getLevelByName($levelName);
    return isset($level) ? self::removeFtByLevel($level, $name) : false;
  }
}