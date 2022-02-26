<?php

namespace palente\luckyblock;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;

class Api {
    
    /**
     * Add an enchantment on an Item (work with PiggyCustomEnchants).
     * @param Item $item
     * @param string $enchantName
     * @param int $enchantLevel
     * @return void
     */
    public function addEnchantment(Item $item, string $enchantName, int $enchantLevel = 1) : void {
        if(isset(Main::getInstance()->piggyPlugin)){
            Main::getInstance()->piggyPlugin->addEnchantment($item, $enchantName, $enchantLevel);
        } else {
            if(is_numeric($enchantName)){
                //TODO: Support this? or at least specify ids are not allowed.
                $enchant = EnchantmentIdMap::getInstance()->fromId($enchantName);
            } else {
                $enchant = StringToEnchantmentParser::getInstance()->parse($enchantName);
            }

            if(isset($enchant)) {
                $item->addEnchantment(new EnchantmentInstance($enchant, $enchantLevel));
            }
        }
    }

    public function parseItem(string $idMeta) : ?Item{
        $argArr = explode(":", $idMeta);
        $item = null;
        if(count($argArr) === 1){
            $item = ItemFactory::getInstance()->get((int)$argArr[0]);
        }else{
            // id:meta
            $item = ItemFactory::getInstance()->get((int)$argArr[0], $argArr[1]);
        }
        return $item;
    }
}