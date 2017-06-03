<?php

namespace EmreTr1;

use pocketmine\entity\{Entity, Zombie};
use pocketmine\Player;

class Text extends Zombie{
	
	public function onUpdate($tick){
		if($this->closed) return;
		
		if(time() % 3 == 0){
		 $this->setNameTag(LiveTexts::replaceText($this->getNameTag()));
		}
	}
	
	public function spawnTo(Player $p){
		$this->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, true);
		$this->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_IMMOBILE, true);
		$this->setNameTag(LiveTexts::replaceForPlayer($p, $this->getNameTag()));
		
		parent::spawnTo($p);
	}
}
?>
