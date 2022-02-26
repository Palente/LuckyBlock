<?php

namespace palente\luckyblock\events;

use cooldogedev\BedrockEconomy\api\BedrockEconomyAPI;
use onebone\economyapi\EconomyAPI;
use palente\luckyblock\Main;
use palente\luckyblock\tasks\SetBlock;
use pocketmine\block\Block;
use pocketmine\block\BlockFactory;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\Player\Player;
use pocketmine\Server;

class BlockBreak implements Listener {

    /**
     * When a player breaks a block.
     * @param BlockBreakEvent $event
     * @return void
     * 
     * @priority LOW
     */
    public function onBreak(BlockBreakEvent $event) : void {
        if ($event->isCancelled()) {
            return;
        }
        $blockItem = Main::getApi()->parseItem(Main::getDefaultConfig()->get("block"));
        if($blockItem instanceof Item) {
            $luckyblock = $blockItem->getBlock();
            $player = $event->getPlayer();
            $block = $event->getBlock();
            if ($block->getId() === $luckyblock->getId() && $block->getMeta() === $luckyblock->getMeta()) {
                $event->setDrops($this->brokeLuckyBlock($player, $block));
            }
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
        $items = array();
        switch($type){
            case "items":

                foreach($loot["items"] as $itemString){
                    if(strpos($itemString, "-")){
                        $array = explode("-", $itemString);

                        $item = Main::getApi()->parseItem($array[0]);
                        if($item instanceof Item){
                            if(isset($array[1])) {
                                $item->setCount($array[1]);
                            }
                            if(isset($array[2]) && $array[2] !== "DEFAULT") {
                                $item->setCustomName(str_replace("{playerName}", $player->getName(), $array[2]));
                            }

                            if(isset($array[3])){
                                if(isset($array[4])){
                                    Main::getApi()->addEnchantment($item, $array[3], $array[4]);
                                } else {
                                    Main::getApi()->addEnchantment($item, $array[3]);
                                }
                            }
                        }else{
                            $player->sendMessage(Main::PREFIX . "An error has occurred, contact an administrator. LUCKYBLOCK_ITEM");
                            Main::getInstance()->getLogger()->warning("Error, you gave an invalid item or the plugin is unable to get the item '$array[0]'");
                        }

                    } else {
                        $item = Main::getApi()->parseItem($itemString);
                        if(!$item instanceof Item){
                            $player->sendMessage(Main::PREFIX . "An error has occurred, contact an administrator. LUCKYBLOCK_ITEM");
                            Main::getInstance()->getLogger()->warning("Error, you gave an invalid item or the plugin is unable to get the item '$itemString'");
                        }
                    }

                    $items[] = $item;
                }

            break;

            case "block":
                $blockId = $loot["block"];

                if(strpos($blockId, ":")){
                    $array = explode(":", $blockId);
                    $blockInstance = BlockFactory::getInstance()->get((int)$array[0], (int)$array[1]);
                } else {
                    $blockInstance = BlockFactory::getInstance()->get((int)$blockId, 0);
                }
                
                Main::getInstance()->getScheduler()->scheduleDelayedTask(new SetBlock($block->getPosition()->getWorld(), $block->getPosition()->asVector3(), $blockInstance), 1);
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
                    Main::getInstance()->getServer()->dispatchCommand(new ConsoleCommandSender(Server::getInstance(), Server::getInstance()->getLanguage()), $cmd);
                }
            break;

            case "money":
                if(isset(Main::getInstance()->economyPlugin)){
                    $moneyCount = $loot["money"];

                    $player->sendMessage(Main::PREFIX . "You won " . $moneyCount . Main::getInstance()->economyPlugin->getMonetaryUnit() . ", congratulation !");
                    EconomyAPI::getInstance()->addMoney($player, $moneyCount);
                }elseif(Main::getInstance()->bedrockEconomy){
                    $moneyCount = $loot["money"];

                    $player->sendMessage(Main::PREFIX . "You won " . $moneyCount  . "$, congratulation !");
                    BedrockEconomyAPI::getInstance()->addToPlayerBalance($player->getName(), $moneyCount) ;
                }
                else {
                    $player->sendMessage(Main::PREFIX . "An error has occurred, contact an administrator. LUCKYBLOCK_ECONOMY");
                    Main::getInstance()->getLogger()->warning("Error, you used a money loot, this is not possible because you have disabled the use of an Economy Plugin (EconomyAPI\BedrockEconomy).");
                }
            break;

            default:
                Main::getInstance()->getLogger()->warning("Error, you used a type of loot that does not exist. Please change this in the configuration.");
            break;
        }

        if(in_array($type, array("block", "money", "commands-player", "commands-server")) || count($items) === 0){
            return array(ItemFactory::air());
        }
        return $items;
    }
}