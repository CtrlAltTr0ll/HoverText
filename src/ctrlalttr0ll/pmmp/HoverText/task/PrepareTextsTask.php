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

namespace ctrlalttr0ll\pmmp\HoverText\task;

use pocketmine\level\Position;
use pocketmine\scheduler\Task;
use pocketmine\Server;
use pocketmine\utils\TextFormat;
use ctrlalttr0ll\pmmp\HoverText\Core;
use ctrlalttr0ll\pmmp\HoverText\data\Data;
use ctrlalttr0ll\pmmp\HoverText\data\FloatingTextData;
use ctrlalttr0ll\pmmp\HoverText\data\UnremovableFloatingTextData;
use ctrlalttr0ll\pmmp\HoverText\i18n\Lang;
use ctrlalttr0ll\pmmp\HoverText\text\FloatingText;
use ctrlalttr0ll\pmmp\HoverText\text\UnremovableFloatingText;
use ctrlalttr0ll\pmmp\HoverText\HoverTextApi;
use function count;

/**
 * Class PrepareTextsTask
 * @package ctrlalttr0ll\pmmp\HoverText\task
 */
class PrepareTextsTask extends Task {

  /** @var Server */
  private $server;
  /** @var array */
  private $ufts;
  /** @var int */
  private $uftsCount = 0;
  /** @var int */
  private $uftsMax;
  /** @var array */
  private $fts;
  /** @var int */
  private $ftsCount = 0;
  /** @var int */
  private $ftsMax;

  public function __construct() {
    $this->server = Server::getInstance();
    $this->ufts = UnremovableFloatingTextData::make()->getData();
    $this->uftsMax = count($this->ufts);
    $this->fts = FloatingTextData::make()->getData();
    $this->ftsMax = count($this->fts);
  }

  public function onRun(int $tick) {
    if ($this->uftsCount === $this->uftsMax) {
      if ($this->ftsCount === $this->ftsMax) {
        $this->onSuccess();
      }else {
        $data = $this->fts[$this->ftsCount];
        $textName = $data[Data::KEY_NAME];
        $loaded = Server::getInstance()->isLevelLoaded($data[Data::KEY_LEVEL]);
        $canLoad = true;
        if (!$loaded) $canLoad = $this->server->loadLevel($data[Data::KEY_LEVEL]);
        if ($canLoad) {
          $level = $this->server->getLevelByName($data[Data::KEY_LEVEL]);
          if ($level !== null) {
            $x = $data[Data::KEY_X];
            $y = $data[Data::KEY_Y];
            $z = $data[Data::KEY_Z];
            $pos = new Position($x, $y, $z, $level);
            $title = $data[Data::KEY_TITLE];
            $text = $data[Data::KEY_TEXT];
            $owner = $data[FloatingTextData::KEY_OWNER];
            $ft = new FloatingText($textName, $pos, $title, $text, $owner);
            HoverTextApi::registerText($ft);
          }
        }
        ++$this->ftsCount;
      }
    }else {
      $data = $this->ufts[$this->uftsCount];
      $textName = $data[Data::KEY_NAME];
      $loaded = $this->server->isLevelLoaded($data[Data::KEY_LEVEL]);
      $canLoad = true;
      if (!$loaded) $canLoad = $this->server->loadLevel($data[Data::KEY_LEVEL]);
      if ($canLoad) {
        $level = $this->server->getLevelByName($data[Data::KEY_LEVEL]);
        if ($level !== null) {
          $x = $data[Data::KEY_X];
          $y = $data[Data::KEY_Y];
          $z = $data[Data::KEY_Z];
          $pos = new Position($x, $y, $z, $level);
          $title = $data[Data::KEY_TITLE];
          $text = $data[Data::KEY_TEXT];
          $uft = new UnremovableFloatingText($textName, $pos, $title, $text);
          HoverTextApi::registerText($uft);
        }
      }
      ++$this->uftsCount;
    }
  }

  private function onSuccess(): void {
    $plugin = $this->server->getPluginManager()->getPlugin("HoverText");
    if ($plugin !== null && $plugin->isEnabled()) {
      $message = Lang::fromConsole()->translateString("on.enable.prepared", [
        count(HoverTextApi::getUfts(), COUNT_RECURSIVE) - count(HoverTextApi::getUfts()),
        count(HoverTextApi::getFts(), COUNT_RECURSIVE) - count(HoverTextApi::getFts())
      ]);
      $core = Core::get();
      $core->getLogger()->info(TextFormat::GREEN . $message);
      $core->getScheduler()->cancelTask($this->getTaskId());
    }
  }
}
