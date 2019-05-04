<?php

namespace palente\luckyblock\tasks;

use pocketmine\scheduler\Task;

use pocketmine\level\Level;

use pocketmine\math\Vector3;

use pocketmine\block\Block;

class SetBlock extends Task {

    /** @var Level $level */
    public $level;

    /** @var Vector3 $pos */
    public $pos;

    /** @var Block $replaceBlock */
    public $replaceBlock;

    public function __construct(Level $level, Vector3 $pos, Block $replaceBlock){
        $this->level = $level;
        $this->pos = $pos;
        $this->replaceBlock = $replaceBlock;
    }

    public function onRun($tick){
        $this->level->getLevel()->setBlock($this->pos, $this->replaceBlock);
    }
}