<?php

namespace Palente\LuckyBlock\utils;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;

use pocketmine\Player;

use pocketmine\Server;

use pocketmine\item\Item;

use pocketmine\block\Block;
use pocketmine\block\BlockFactory;

use pocketmine\command\ConsoleCommandSender;

use pocketmine\math\Vector3;

use Palente\LuckyBlock\Main;

class Events implements Listener {

    public $eco;
    public $caller;
	public $cnf;
	
    public function __construct(MN $caller){
    	$this->caller = $caller;
    }

    public function onBreak(BlockBreakEvent $event){
    	$block = $event->getBlock();
    	$player = $event->getPlayer();
		$config = Main::getInstance()->config;
		
		if($event->isCancelled()) return;

		if($block->getId() == Main::getInstance()->config->get("LuckyBlockId")){
			$nbchance = mt_rand(0, 20);
			$loot = $config->get("Chance-" . $nbchance);

			if(empty($loot["Type"])){
				$player->sendPopup(Main::getInstance()->prefix . "Anything winned.");
    			$event->setDrops(array(Item::get(0, 0, 0));

				return;
			}

			switch($loot["Type"]){
				case "items":
					$item = $loot["idItems"];
					$amount = $loot["amountItems"];

					$event->setDrops(array(Item::get($item, 0, $amount));
					$player->sendPopup("You winned Item !!");
				break;

				case "blocks":
					$theblock = $loot["idBlocks"];

					$block->getLevel()->setBlock($block->asPosition()->asVector3(), Block::get($theblock), true);
					$event->setDrops(array(Item::get(0, 0, 0));
					$event->setCancelled();
				break;

				case "money":
					if(Main::getInstance()->mode_eco){
						$money = $loot["moneyToAdd"];

						Main::getInstance()->EconomyAPI->addMoney($player, $money);
						$player->sendMessage(Main::getInstance()->prefix."You winned ".$money." money! §aCongratulation!§a");
						$event->setDrops(array(Item::get(0, 0, 0));
					} else {
						Main::$logger->warning("Usage of The type money in the case ".$nbchance." but economy is disabled..");
						$player->sendMessage(Main::getInstance()->prefix."Oups.. Error has occured.. No gain found");
						$event->setDrops(array(Item::get(0, 0, 0));
					}
				break;

				case "commands":
					$cmd = $loot["command"];
					$cmd = str_replace(":nameofplayer:", $player->getName(), $cmd);
					
					if($loot["executor"] == "player"){
						Main::getInstance()->getServer()->dispatchCommand($player, $cmd);
						$player->sendPopup(Main::getInstance()->prefix."executing command..");
					} elseif ($loot["executor"] ==  "console"){
						Main::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), $cmd);
						$player->sendPopup(Main::getInstance()->prefix."executing command..");
					} else {
						Main::$logger->warning("Usage of The type command in the case ".$nbchance." but the executor is not player or command it\"s ".$loot["executor"]);
						$player->sendMessage(Main::getInstance()->prefix."Oups.. error has occured.. No gain found for Commands");
					}

					$event->setDrops(array(Item::get(0, 0, 0));
				break;

				case "enchant":
					if(Main::getInstance()->mode_enc && isset($loot["idItems"], $loot["amountItems"], $loot["enchantName"], $loot["enchantLevel"])){
						$item = $loot["idItems"];
						$amount = $loot["amountItems"];
						$item = Item::get($item, 0, $amount);
						$enc = $loot["enchantName"];
						$encl = $loot["enchantLevel"];
						Main::getInstance()->piggy->addEnchantment($item, $enc, $encl);
						$event->setDrops([$item]);
						$player->sendPopup(Main::getInstance()->prefix."You get an enchanted item");
					} else {
						Main::$logger->warning("Usage of The type enchant in the case ".$nbchance." but one of them is empty OR Piggy is not available");
						$player->sendMessage(Main::getInstance()->prefix."Oups.. error has occured.. No gain found for Enchant");
					}
				break;
			}

		}
    }
}
