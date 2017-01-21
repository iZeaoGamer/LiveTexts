<?php

   /**
    * LiveText Entity class
    *
    * @author EmreTr1
    */
   
   namespace EmreTr1;
   
   use pocketmine\Server;
   use pocketmine\Player;
   use pocketmine\item\Item;
   use pocketmine\math\Vector3;
   use pocketmine\utils\UUID;
   use pocketmine\entity\Entity;
   use pocketmine\entity\Human;
   use pocketmine\nbt\tag\{CompoundTag, ListTag};
   use pocketmine\level\format\Chunk;
   use pocketmine\event\entity\{EntityDamageEvent, EntityDamageByEntityEvent};
   use pocketmine\network\protocol\AddPlayerPacket;
   
   use EmreTr1\Events\TapLiveTextEvent;
   
   class Text extends Human{
   	
   	public $text;
   	
   	public function __construct(Chunk $chunk, CompoundTag $nbt){
	   	parent::__construct($chunk, $nbt);
		  $this->text = $nbt->CustomName;
		  $main = LiveTexts::getInstance();
		  
		  if(!isset($nbt->Permissions)){
		  	 $nbt->Permissions = new ListTag("Permissions", []);
		  }
		  
		  $ltname = $nbt->LiveTextName->getValue();
		  $cfg = $main->config->get("LiveTexts")[$ltname];
		  if(isset($cfg['Permissions']) and !empty($cfg['Permissions'])){
		  	 foreach($cfg['Permissions'] as $perm){
		  	 	 $nbt->Permissions->{trim($perm)} = new StringTag($perm, $perm);
		  	 }
		  }
	  }
    
   	public function attack($damage, EntityDamageEvent $source){
   		$source->setCancelled(true);
   		if($source instanceof EntityDamageByEntityEvent){
   			if($source->getDamager() instanceof Player){
   		  Server::getInstance()->getPluginManager()->callEvent(new TapLiveTextEvent($source->getDamager(), $this, 0)); // more Causes Soon...
   		 }
   		}
   		parent::attack($damage, $source);
   	}
   	
   	public function onUpdate($currentTick){
   		if($this->closed or !isset($this->namedtag->infos)) return false;
   		
   		$info=$this->namedtag->infos;
   		$file=$info["file"];
   		if($file!=""){
   		 $data=$info["datafolder"];
   		 $tag = file_get_contents($data);
   		 $text=LiveTexts::getInstance()->replacedText($tag);
   		 $this->setNameTag($text);
   		}else{
   			$tag=$this->getNameTag(); //fixed now! No update bug !
   			$text=LiveTexts::getInstance()->replacedText($tag);
   			$this->setNameTag($text);
   		}
   		
   		return true;
   	}
   	
   	public function spawnTo(Player $p){
   		if(!empty($this->getPermissions())){
   			foreach($this->getPermissions() as $perm){  // if the player has permission
   				if(!$p->hasPermission($perm->getValue())){
   					return false;
   				}
   			}
   		}
   		$main=LiveTexts::getInstance();
     $pk = new AddPlayerPacket();
     $pk->eid = $this->getId();
     $pk->uuid = UUID::fromRandom();
     $pk->x = $this->x;
     $pk->y = $this->y;
     $pk->z = $this->z;
     $pk->speedX = 0;
     $pk->speedY = 0;
     $pk->speedZ = 0;
     $pk->yaw = 0;
     $pk->pitch = 0;
     $pk->item = Item::get(0);
     $this->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, true);
     $this->setNameTag($main->replaceForPlayer($p, $this->text));
     $this->setNameTagAlwaysVisible(true);
     $this->setNameTagVisible(true);
     $pk->metadata = $this->dataProperties;
     $p->dataPacket($pk);
     Server::getInstance()->removePlayerListData($this->getUniqueId(), [$p]);	
   	}
   	
   	public function hasFile() : bool{
   		return isset($this->namedtag["infos"]) ? true : false;
   	}
   	
   	public function getPermissions() : array{
   		return $this->namedtag->Permissions;
   	}
   }
?>