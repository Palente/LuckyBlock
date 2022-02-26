<?php

namespace palente\luckyblock\tasks;

use pocketmine\block\Block;
use pocketmine\math\Vector3;
use pocketmine\scheduler\Task;
use pocketmine\world\World;

class SetBlock extends Task {

    /** @var World $world */
    public World $world;

    /** @var Vector3 $pos */
    public Vector3 $pos;

    /** @var Block $replaceBlock */
    public Block $replaceBlock;

    public function __construct(World $world, Vector3 $pos, Block $replaceBlock)
    {
        $this->world = $world;
        $this->pos = $pos;
        $this->replaceBlock = $replaceBlock;
    }

    public function onRun() : void
    {
        $this->world->setBlock($this->pos, $this->replaceBlock);
    }
}