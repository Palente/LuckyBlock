<?php

namespace palente\luckyblock;

use pocketmine\item\Item;
use pocketmine\item\enchantment\Enchantment;
use pocketmine\item\enchantment\EnchantmentInstance;

class Api {
    
    /**
     * Add an enchantment on an Item (work with PiggyCustomEnchants).
     * @param Item $item
     * @param string $enchantName
     * @param int $enchantLevel
     * @return void
     */
    public function addEnchantment(Item &$item, string $enchantName, int $enchantLevel = 1) : void {
        if(isset(Main::getInstance()->piggyPlugin)){
            Main::getInstance()->piggyPlugin->addEnchantment($item, $enchantName, $enchantLevel);
        } else {
            if(is_numeric($enchantName)){
                $enchant = Enchantment::getEnchantment($enchantName);
            } else {
                $enchant = Enchantment::getEnchantmentByName($enchantName);
            }

            if(isset($enchant)) $item->addEnchantment(new EnchantmentInstance($enchant, $enchantLevel));
        }
    }
}