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

use pocketmine\scheduler\AsyncTask;
use pocketmine\Server;
use pocketmine\utils\VersionString;
use ctrlalttr0ll\pmmp\HoverText\Core;
use function curl_init;
use function curl_setopt_array;
use function curl_exec;
use function curl_errno;
use function curl_error;
use function curl_close;
use function json_decode;

/**
 * CheckUpdateTaskClass
 */
class CheckUpdateTask extends AsyncTask {

  public function onRun() {
    $curl = curl_init();
    curl_setopt_array($curl, [
      CURLOPT_URL => "https://api.github.com/repos/CtrlAltTr0ll/HoverText/releases",
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_USERAGENT => "php_".PHP_VERSION,
      CURLOPT_SSL_VERIFYPEER => false
    ]);
    $json = curl_exec($curl);
    $errorNo = curl_errno($curl);
    if ($errorNo) {
      $error = curl_error($curl);
      throw new \Exception($error);
    }
    curl_close($curl);
    $data = json_decode($json, true);
    $this->setResult($data);
  }

  public function onCompletion(Server $server){
    $core = Core::get();
    if ($core->isEnabled()) {
      $data = $this->getResult();
      if (isset($data[0])) {
        $ver = new VersionString($data[0]["name"]);
        $core->compareVersion(true, $ver, $data[0]["html_url"]);
      }else {
        $core->compareVersion(false);
      }
    }
  }
}
