<?php
namespace EmreTr1;
use pocketmine\event\entity\{EntityDamageEvent, EntityDamageByEntityEvent};
use pocketmine\nbt\tag\{CompoundTag, ListTag, DoubleTag, FloatTag, StringTag};
use pocketmine\entity\Entity;
use pocketmine\{Server, Player};

class EventListener implements Listener {

public function onDamage(EntityDamageEvent $event){
		if($event instanceof EntityDamageByEntityEvent){
			$p = $event->getDamager();
			$entity = $event->getEntity();
			if(isset($this->plugin->whatid[$p->getName()])){
				$id = $entity->getId();
				$p->sendMessage($this->prefix."§aEntity ID: {$id}");
				return true;
				unset($this->plugin->whatid[$p->getName()]);
			}
		}
	}
	
	public function addLiveText($pos, string $text){
	$nbt = new CompoundTag;
	$nbt->Pos = new ListTag("Pos", [new DoubleTag("", $pos->x), new DoubleTag("", $pos->y), new DoubleTag("", $pos->z)]);
	$nbt->Rotation = new ListTag("Rotation", [new FloatTag("", 0), new FloatTag("", 0)]);
	$nbt->Motion = new ListTag("Pos", [new DoubleTag("", 0), new DoubleTag("", 0), new DoubleTag("", 0)]);
   $nbt->baseText = new StringTag("baseText", $text);
	$entity = Entity::createEntity("Text", $pos->level, $nbt);
	$entity->spawnToAll();
	}
	
	public static function replacedText(string $text){ 
		$server = Server::getInstance();
		$tps=$server->getTicksPerSecond();
		$onlines=count($server->getOnlinePlayers());
		$maxplayers=$server->getMaxPlayers();
		$worldsc=count($server->getLevels());
		$variables=[
		"{line}"=>"\n",
		"{tps}"=>$tps,
		"{maxplayers}"=>$maxplayers,
		"{onlines}"=>$onlines,
		"{worldscount}"=>$worldsc,
		"{ip}"=>$server->getIp(),
		"{port}"=>$server->getPort(),
		"{motd}"=>$server->getMotd(),
		"{network}"=>$server->getNetwork()->getName()];
		
		foreach($variables as $var=>$ms){
			$text=str_ireplace($var, $ms, $text);
		}
		return $text;
	}
   	
	public static function replaceForPlayer(Player $p, string $text){
		$specialvars = [
		"{name}"=>$p->getName(),
		"{nametag}"=>$p->getNameTag(),
		"{hunger}"=>$p->getFood(),
		"{health}"=>$p->getHealth(),
		"{maxhealth}"=>$p->getMaxHealth(),
		"{nbt}"=>$p->namedtag,
		"{level}"=>$p->getLevel()->getFolderName()];
		
		foreach($specialvars as $var=>$ms){
			$text = str_ireplace($var, $ms, $text);
		}
		return $text;
	}
	
}