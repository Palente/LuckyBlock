<?php

namespace palente\luckyblock;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockBreakEvent;

use pocketmine\Player;

use pocketmine\Server;

use pocketmine\item\Item;

use pocketmine\block\Block;

use pocketmine\command\ConsoleCommandSender;

use palente\luckyblock\Main;

class Events implements Listener {

    /**
     * When a player breaks a block.
     * @param BlockBreakEvent $event
     * @return void
     */
    public function onBreak(BlockBreakEvent $event) : void {
        $luckyblock = Item::fromString(Main::getDefaultConfig()->get("block"));
        $player = $event->getPlayer();
        $block = $event->getBlock();
        
        if($event->isCancelled()) return;

        if($block->getId() === $luckyblock->getId() and $block->getDamage() === $luckyblock->getDamage()){
            $event->setDrops($this->brokeLuckyBlock($player, $block));
        }
    }

    /**
     * When a player break the LuckyBlock.
     * @param Player $player
     * @param Block $block
     * @return Item[]
     */
    private function brokeLuckyBlock(Player $player, Block $block) : array {
        $loot = Main::getDefaultConfig()->get("loot");

        shuffle($loot);

        $loot = array_shift($loot);
        $type = array_keys($loot)[0];

        switch($type){
            case "items":
                $items = array();

                foreach($loot["items"] as $item){
                    if(strpos($item, "-")){
                        $array = explode("-", $item);

                        $item = Item::fromString($array[0]);

                        if(isset($array[1])) $item->setCount($array[1]);
                        if(isset($array[2]) and $array[2] != "DEFAULT") $item->setCustomName(str_replace("{playerName}", $player->getName(), $array[2]));

                        if(isset($array[3])){
                            //TODO: fix this:
                            if(isset($array[4])){
                                //Main::getInstance()->piggyPlugin->addEnchantment($item, $array[3], $array[4]);
                            } else {
                                //Main::getInstance()->piggyPlugin->addEnchantment($item, $array[3]);
                            }
                        }
                    } else {
                        $item = Item::fromString($item);
                    }

                    $items[] = $item;
                }

                return $items;
            break;

            case "block":
                $blockId = $loot["block"];

                if(strpos($blockId, ":")){
                    $array = explode(":", $blockId);
                    $blockInstance = Block::get($array[0], $array[1]);
                } else {
                    $blockInstance = Block::get($blockId);
                }

                $block->getLevel()->setBlock($block->asPosition()->asVector3(), $blockInstance);
            break;

            case "commands-player":
                foreach($loot["commands-player"] as $cmd){
                    $cmd = str_replace("{playerName}", $player->getName(), $cmd);

                    //TODO: add custom message for the player.
                    Main::getInstance()->getServer()->dispatchCommand($player, $cmd);
                }
            break;

            case "commands-server":
                foreach($loot["commands-server"] as $cmd){
                    $cmd = str_replace("{playerName}", $player->getName(), $cmd);

                    //TODO: add custom message for the player.
                    Main::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(), $cmd);
                }
            break;

            case "money":
                if(isset(Main::getInstance()->economyPlugin)){
                    $moneyCount = $loot["money"];

                    $player->sendMessage(Main::PREFIX . "You winned " . $moneyCount . Main::getInstance()->economyPlugin->getMonetaryUnit() . ", congratulation !");
                    Main::getInstance()->economyPlugin->addMoney($player, $moneyCount);
                } else {
                    $player->sendMessage(Main::PREFIX . "An error has occurred, contact an administrator.");
                    Main::getInstance()->getLogger()->warning("Error, you used a money loot, this is not possible because you have disabled the use of EconomyAPI.");
                }
            break;

            default:
                Main::getInstance()->getLogger()->warning("Error, you used a type of loot that does not exist. Please change this in the configuration.");
            break;
        }

        if(in_array($type, array("block", "money", "commands-player", "commands-server"))){
            return array(Item::get(0, 0, 0));
        }
    }
}
