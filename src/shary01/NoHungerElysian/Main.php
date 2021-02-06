<?php


namespace shary01\NoHungerElysian;


use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerExhaustEvent;

use pocketmine\Player;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\TextFormat;


class Main extends PluginBase implements Listener {

	private $settings;

	public function onEnable() : void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);

		if(!is_dir($this->getDataFolder())) mkdir($this->getDataFolder());

		$this->settings = new Config($this->getDataFolder() . "settings.yml", Config::YAML, [
			"x1" => 0,
			"y1" => false,
			"z1" => 0,

			"x2" => 0,
			"y2" => false,
			"z2" => 0
		]);
	}

	public function onExhaust(PlayerExhaustEvent $event) : void{

		$player = $event->getPlayer();

		$pos = $player->asVector3();

		$x1 = $this->settings->get("x1");
		$y1 = $this->settings->get("y1");
		$z1 = $this->settings->get("z1");

		$x2 = $this->settings->get("x2");
		$y2 = $this->settings->get("y2");
		$z2 = $this->settings->get("z2");

		if(($this->settings->get("y1") or $this->settings->get("y2")) == false){

			if((min($x1,$x2) <= $pos->getX()) && (max($x1,$x2) >= $pos->getX()) && (min($z1,$z2) <= $pos->getZ()) && (max($z1,$z2) >= $pos->getZ())){
				$event->setCancelled(true);
			}

		} elseif((min($x1, $x2) <= $pos->getX()) && (max($x1, $x2) >= $pos->getX()) && (min($y1, $y2) <= $pos->getY()) && (max($y1, $y2) >= $pos->getY()) && (min($z1, $z2) <= $pos->getZ()) && (max($z1, $z2) >= $pos->getZ())) {
			$event->setCancelled(true);
		}
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args) : bool {

		if(!$sender instanceof Player){
			$sender->sendMessage("This command is allowed only in game");
			return false;
		}

		if(!$sender->hasPermission('fly.command') || !$sender->isOp()){
			$sender->sendMessage(TextFormat::RED."You don't have permission to use this command.");
			return false;
		}

		if(strtoupper($command->getName()) == "HUNGER"){

			if(empty($args)){
				$sender->sendMessage(TextFormat::GRAY."Usage> /hunger pos1:pos2");
				return false;
			}

			if(strtoupper($args[0]) == "POS1" or strtoupper($args[0]) == "POS2"){

				$type = substr($args[0], -1);

				foreach(["x", "y", "z"] as $key){
					$this->settings->set($key.$type, (int) $sender->{$key});
				}

				$sender->sendMessage(TextFormat::GREEN . "Position $type set!");
			} else {
				$sender->sendMessage(TextFormat::GRAY."Usage> /hunger pos1:pos2");
				return false;
			}
		}

		return true;
	}
}
