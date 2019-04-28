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
	
	/**
	 * When a player breaks a block.
	 * @param BlockBreakEvent $event
	 * @return void
	 */
    public function onBreak(BlockBreakEvent $event) : void {
		$player = $event->getPlayer();
    	$block = $event->getBlock();
		
		if($event->isCancelled()) return;

		//TODO: document this:
		//TODO: add custom messages:

		if($block->getId() == Main::getDefaultConfig()->get("LuckyBlockId")){
			$nbchance = mt_rand(0, 20);
			//TODO: addition of a chance counter, this must be done automatically by the plugin !
			$loot = Main::getDefaultConfig()->get("Chance-" . $nbchance);

			if(empty($loot["Type"])){
				$player->sendPopup(Main::getInstance()->prefix . "Anything winned.");
    			$event->setDrops(array(Item::get(0, 0, 0));

				return;
			}

			if(in_array($loot["Type"], array("blocks", "money", "commands"))){
				$event->setDrops(array(Item::get(0, 0, 0));
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
				break;

				case "commands":
					$cmd = $loot["command"];
					$cmd = str_replace(":nameofplayer:", $player->getName(), $cmd);
					
					switch($loot["executor"]){
						case "player":
							Main::getInstance()->getServer()->dispatchCommand($player, $cmd);
							$player->sendPopup(Main::getInstance()->prefix."executing command..");
						break;

						case "executor":
							Main::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), $cmd);
							$player->sendPopup(Main::getInstance()->prefix . "executing command..");
						break;

						default:
							Main::getInstance()->getLogger()->warning("Usage of The type command in the case " . $nbchance . " but the executor is not player or command it\"s " . $loot["executor"]);
							$player->sendMessage(Main::getInstance()->prefix . "Oups.. error has occured.. No gain found for Commands");
						break;
					}
				break;

				case "enchant":
					if(Main::getInstance()->mode_enc and isset($loot["idItems"], $loot["amountItems"], $loot["enchantName"], $loot["enchantLevel"])){
						$item = $loot["idItems"];
						$amount = $loot["amountItems"];
						$enchant = $loot["enchantName"];
						$enchantLevel = $loot["enchantLevel"];

						$item = Item::get($item, 0, $amount);

						Main::getInstance()->piggyPlugin->addEnchantment($item, $enchant, $enchantLevel);

						$player->sendPopup(Main::getInstance()->prefix."You get an enchanted item.");
						$event->setDrops(array($item));
					} else {
						Main::getInstance()->getLogger()->warning("Usage of The type enchant in the case " . $nbchance . " but one of them is empty OR Piggy is not available");
						$player->sendMessage(Main::getInstance()->prefix . "Oups.. error has occured.. No gain found for Enchant");
					}
				break;

				case "money":
					if(Main::getInstance()->mode_eco){
						$money = $loot["moneyToAdd"];

						Main::getInstance()->economyPlugin->addMoney($player, $money);
						$player->sendMessage(Main::getInstance()->prefix."You winned " . $money . " money ! §aCongratulation !");
					} else {
						Main::getInstance()->getLogger()->warning("Usage of The type money in the case " . $nbchance . " but economy is disabled..");
						$player->sendMessage(Main::getInstance()->prefix."Oups.. Error has occured.. No gain found");
					}
				break;
			}
		}
    }
}
