<?php

namespace EmreTr1;

use pocketmine\entity\{Entity, Zombie};
use pocketmine\Player;
use pocketmine\event\entity\EntityDamageEvent;

class Text extends Zombie{
	
	public function attack($damage, EntityDamageEvent $event){
		$event->setCancelled(true);
		parent::attack($damage, $event);
	}
	
	public function onUpdate($tick){
		if($this->closed) return;
		
		if(time() % 3 == 0){
		 $this->setNameTag(LiveTexts::replacedText($this->getNameTag()));
		}
	}
	
	public function spawnTo(Player $p){
		$this->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, true);
		$this->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_IMMOBILE, true);
		$this->setNameTagAlwaysVisible(true);
		$this->setNameTagVisible(true);
		$this->setNameTag(LiveTexts::replaceForPlayer($p, $this->getNameTag()));
		
		parent::spawnTo($p);
	}
}
?>