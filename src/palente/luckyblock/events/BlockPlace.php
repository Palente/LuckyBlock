<?php

namespace palente\luckyblock\events;

use pocketmine\event\Listener;
use pocketmine\event\block\BlockPlaceEvent;

class BlockPlace implements Listener {
    
    /**
     * When a player place a block.
     * @param BlockPlaceEvent $event
     * @return void
     * 
     * @priority LOW
     */
    public function onPlace(BlockPlaceEvent $event){
        //TODO...
    }
}