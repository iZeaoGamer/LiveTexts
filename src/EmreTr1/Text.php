<?php

namespace EmreTr1;

use pocketmine\entity\{Entity, Zombie, FallingSand};
use pocketmine\Player;
use pocketmine\event\entity\EntityDamageEvent;

class Text extends FallingSand{
	
	protected function initEntity(){
		Entity::initEntity();
		$this->setDataProperty(Entity::DATA_VARIANT, Entity::DATA_TYPE_INT, 0 | (0 << 8));
	}
	
	public function attack($damage, EntityDamageEvent $event){
		$event->setCancelled(true);
		parent::attack($damage, $event);
	}
	
	public function onUpdate($tick){
		if($this->closed) return;
		$this->setNameTag(LiveTexts::replacedText($this->getNameTag()));
	}
	
	public function spawnTo(Player $p){
		$this->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_IMMOBILE, true);
		$this->setNameTag(LiveTexts::replaceForPlayer($p, $this->getNameTag()));
		
		$this->setNameTagVisible(true);
		$this->setNameTagAlwaysVisible(true);
		
		parent::spawnTo($p);
	}
}
?>