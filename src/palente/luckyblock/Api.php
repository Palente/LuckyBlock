<?php

namespace palente\luckyblock;

use pocketmine\data\bedrock\EnchantmentIdMap;
use pocketmine\item\enchantment\EnchantmentInstance;
use pocketmine\item\enchantment\StringToEnchantmentParser;
use pocketmine\item\Item;
use pocketmine\block\Block;
use pocketmine\data\bedrock\block\BlockStateDeserializeException;
use pocketmine\item\ItemBlock;
use pocketmine\item\LegacyStringToItemParser;
use pocketmine\item\LegacyStringToItemParserException;
use pocketmine\item\StringToItemParser;
use pocketmine\world\format\io\GlobalBlockStateHandlers;

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
                $enchant = EnchantmentIdMap::getInstance()->fromId((int) $enchantName);
            } else {
                $enchant = StringToEnchantmentParser::getInstance()->parse($enchantName);
            }

            if(isset($enchant)) {
                $item->addEnchantment(new EnchantmentInstance($enchant, $enchantLevel));
            }
        }
    }

    /**
     * Accept modern aliases and retain compatibility with existing id:meta configs.
     */
    public function parseItem(string $identifier) : ?Item {
        $item = StringToItemParser::getInstance()->parse($identifier);
        if($item !== null){
            return $item;
        }
        try{
            return LegacyStringToItemParser::getInstance()->parse($identifier);
        }catch(LegacyStringToItemParserException $e){
            return null;
        }
    }

    public function parseBlock(string $identifier) : ?Block {
        // Block IDs must use the block upgrader: e.g. lava has no item form.
        if(preg_match('/^(\\d+)(?::(\\d+))?$/D', $identifier, $matches) === 1){
            try{
                $state = GlobalBlockStateHandlers::getUpgrader()->upgradeIntIdMeta(
                    (int) $matches[1], (int) ($matches[2] ?? 0)
                );
                return GlobalBlockStateHandlers::getDeserializer()->deserializeBlock($state);
            }catch(BlockStateDeserializeException $e){
                return null;
            }
        }
        $item = $this->parseItem($identifier);
        return $item instanceof ItemBlock ? $item->getBlock() : null;
    }
}
