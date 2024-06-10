<?php

declare(strict_types=1);

namespace Blackjack200\AutoRejoin;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\plugin\PluginBase;
use WeakMap;

class Main extends PluginBase implements Listener {
	private WeakMap $address;

	protected function onEnable() : void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
		$this->address = new WeakMap();
	}

	protected function onDisable() : void {
		if (!$this->getServer()->isRunning()) {
			foreach ($this->getServer()->getOnlinePlayers() as $player) {
				if (isset($this->address[$player])) {
					$address = $this->address[$player];
					$last = strrpos($address, ':');
					$host = substr($address, 0, $last);
					$port = substr($address, $last + 1);
					$player->transfer($host, (int) $port);
				}
			}
		}
	}

	public function onPlayerJoin(PlayerJoinEvent $event) : void {
		$player = $event->getPlayer();
		$data = $player->getPlayerInfo()->getExtraData();
		if (isset($data['ServerAddress'])) {
			$this->address[$player] = $data['ServerAddress'];
		}
	}
}
