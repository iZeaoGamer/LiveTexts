<?php

namespace EmreTr1;

use pocketmine\entity\{Entity, Creature, FallingSand};
use pocketmine\network\mcpe\protocol\AddEntityPacket;
use pocketmine\Player;
use pocketmine\event\entity\EntityDamageEvent;

class Text extends Creature{
	
	const NETWORK_ID = 66;
	
	public function getName() : string{
		return "Text";
	}
	
	protected function initEntity(){
		parent::initEntity();
		$this->setMaxHealth(10);
		$this->setHealth(10);
		$this->setDataProperty(Entity::DATA_VARIANT, Entity::DATA_TYPE_INT, 0 | (0 << 8));
	}
	
	public function attack(EntityDamageEvent $source){
		$event->setCancelled(true);
		parent::attack($source);
	}
	
	public function onUpdate(int $tick): bool{
		if($this->closed) return true;
		$this->setNameTag(LiveTexts::replacedText($this->getBaseText()));
	}

  public function getBaseText(){
   if(!isset($this->namedtag->baseText)) return $this->getNameTag();
   return $this->namedtag->baseText->getValue() ?? $this->getNameTag();
  }
	
	public function spawnTo(Player $p){
		$this->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_IMMOBILE, true);
		
		$this->setNameTagVisible(true);
		$this->setNameTagAlwaysVisible(true);
		
		$pk = new AddEntityPacket();
		$pk->type = self::NETWORK_ID;
		$pk->eid = $this->getId();
		$pk->x = $this->x;
		$pk->y = $this->y;
		$pk->z = $this->z;
		$pk->speedX = $this->motionX;
		$pk->speedY = $this->motionY;
		$pk->speedZ = $this->motionZ;
		$pk->yaw = $this->yaw;
		$pk->pitch = $this->pitch;
		$pk->metadata = $this->dataProperties;
		$p->dataPacket($pk);

		parent::spawnTo($p);
	}
}
