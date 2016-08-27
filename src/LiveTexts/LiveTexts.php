<?php
   namespace LiveTexts;
   
   use pocketmine\plugin\PluginBase;
   use pocketmine\Player;
   use pocketmine\Server;
   use pocketmine\entity\Entity;
   use pocketmine\math\Vector3;
   use pocketmine\event\Listener;
   use pocketmine\command\Command;
   use pocketmine\command\CommandSender;
   use pocketmine\event\player\PlayerJoinEvent;
   use pocketmine\event\entity\EntityDamageEvent;
   use pocketmine\event\entity\EntityDamageByEntityEvent;
   use pocketmine\nbt\tag\CompoundTag;
   use pocketmine\utils\Config;
   use pocketmine\event\player\PlayerMoveEvent;
   use pocketmine\nbt\tag\ByteTag;
   use pocketmine\nbt\tag\ListTag;
   use pocketmine\nbt\tag\DoubleTag;
   use pocketmine\nbt\tag\FloatTag;
   use pocketmine\nbt\tag\ShortTag;
   use pocketmine\nbt\tag\StringTag;
         
   class LiveTexts extends PluginBase implements Listener{
   	
   	public $config;
   	public $texters=array();
   	public $removers=array();
    private static $instance;
   	
   	public static function getInstance(){
   		return self::$instance;
   	}
   	
   	public function onEnable(){
   		self::$instance=$this;
   		$this->getServer()->getPluginManager()->registerEvents($this, $this);
   		Server::getInstance()->getLogger()->info("§dLiveTexts §eStarting...");
   		Server::getInstance()->getCommandMap()->register("FloatingText", new Livecommands("lt"));
   		$this->loadConfig();
   	}
   	
   	public function loadConfig(){
   		$main=LiveTexts::getInstance();
   		 @mkdir($this->getDataFolder());
   		$this->config=new Config($this->getDataFolder() . "texts.yml", Config::YAML);
    		if(!$this->config->get("LiveTexts")){
   			$opt=[
   			"File"=>"welcome.txt"];
   			$this->config->set("LiveTexts", array());
   			$cfg=$this->config->get("LiveTexts");
   			$cfg["Welcome"]=$opt;
   			$this->config->set("LiveTexts", $cfg);
   	    touch($main->getDataFolder()."welcome.txt");
   	    $dosya=fopen($main->getDataFolder()."welcome.txt", "a");
   	    fwrite($dosya, "Welcome to the LiveTexts");
   	    fclose($dosya);
   			$this->config->save();
   		}
   	}
   	
   	public function onDamage(EntityDamageEvent $event){
   		$entity=$event->getEntity();
   		$main=LiveTexts::getInstance();
   		if($event instanceof EntityDamageByEntityEvent){
   			$damager=$event->getDamager();
   			if(isset($main->texters[$entity->getId()]) and (!isset($main->removers[$damager->getName()]))){
   			  $event->setCancelled(true);
   		}
   		  if(isset($main->removers[$damager->getName()])){
   		  	 $entity->kill();
   		  	 $damager->sendMessage("§6[LiveTexts]§c LiveText removed.");
   		  	 unset($main->removers[$damager->getName()]);
   		  }
   		}
   	}
   	
   	public function createLiveText($x, $y, $z, $skin, $skinId, $inv, $yaw, $pitch, $chunk, $tag, $name){
   	 $nbt = new CompoundTag;
		
		 $nbt->Pos = new ListTag("Pos", [
			new DoubleTag("", $x),
      			new DoubleTag("", $y),
      			new DoubleTag("", $z)
       		]);

    		$nbt->Rotation = new ListTag("Rotation", [
    			new FloatTag("", $yaw),
    	 		new FloatTag("", $pitch)
       		]);
       		 $nbt->Inventory = new ListTag("Inventory", $inv);
            $nbt->Skin = new CompoundTag("Skin", ["Data" => new StringTag("Data", $skin), "Name" => new StringTag("Name", $skinId)]);

     		$nbt->Health = new ShortTag("Health", 20);
     		$nbt->Invulnerable = new ByteTag("Invulnerable", 1);
     		$nbt->LiveTextName= new StringTag("LiveTextName", $name);
     		$nbt->CustomName=new StringTag("CustomName", $tag);
   		  $entity=Entity::createEntity("Human", $chunk, $nbt);
   		  $entity->spawnToAll();
   			 $entity->setNameTag("$tag");
    			$entity->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, true);
			  $entity->setDataProperty(Entity::DATA_SHOW_NAMETAG, Entity::DATA_TYPE_BYTE, 1);
   	}
   	
   	public function onJoin(PlayerJoinEvent $event){
   		$p=$event->getPlayer();
   		$levels=Server::getInstance()->getLevels();
   		foreach($levels as $level){
   		$config=LiveTexts::getInstance()->config;
   		$main=LiveTexts::getInstance();
   		$core=LiveTexts::getInstance();
   				$entities=$level->getEntities();
   				foreach($entities as $entity){
   					if(isset($entity->namedtag->LiveTextName)){
   						$cfg=$config->get("LiveTexts");
   						$a=$entity->namedtag->LiveTextName;
   						$ad=$cfg["$a"]["File"];
   				     $dosya=fopen($this->getDataFolder().$ad, "r");
   			      	 $yazi=fread($dosya, filesize($this->getDataFolder().$ad));
   				     fclose($dosya);
   						$this->texters[$entity->getId()]=true;
   						$entity->setNameTag("$yazi");
   						$uuid=$entity->getUniqueId();
   						$this->getServer()->removePlayerListData($uuid, [$p]);
   						$n=$entity->getNameTag();
   						$entity->setNameTag(str_ireplace("ō", " ", $n));
   						$entity->setDataFlag(Entity::DATA_FLAGS, Entity::DATA_FLAG_INVISIBLE, true);
			         $entity->setDataProperty(Entity::DATA_SHOW_NAMETAG, Entity::DATA_TYPE_BYTE, 1);
   					}
   				}
   			}
   	}
   	
   	public function onMove(PlayerMoveEvent $event){
   		$p=$event->getPlayer();
   		$chunks=$event->getPlayer()->getLevel()->getChunks();
   		$main=LiveTexts::getInstance();
   		foreach($chunks as $chunk){
   			$entities=$chunk->getEntities();
   		 foreach($entities as $entity){
   			 if(isset($main->texters[$entity->getId()])){
   				  $uuid=$entity->getUniqueId();
   			  	$this->getServer()->removePlayerListData($uuid, [$p]);
   			}
   		}
   	}
   	}
   }
   
   class Livecommands extends Command{
   	
   	private $name;
   	
   	public function __construct($name){
   		parent::__construct(
   		$name,
   		"LiveTexts plugin main Command",
   		"/lt <add|cancel|remove>");
   		$this->setPermission("livetext.command.use");
   	}
   	
   	public function execute(CommandSender $s, $label, array $args){
                if(!$s->hasPermission("livetext.command.use")){
                 return true;
                }
   		if(!empty($args[0])){
   			$main=LiveTexts::getInstance();
   			$core=LiveTexts::getInstance();
   			switch($args[0]){
   				case "add":
   				    if(!empty($args[1])){
   				    	  $file=$args[1];
   				    	  if($main->config->getNested("LiveTexts.$file")){
   				    	  	  $ad=$main->config->getNested("LiveTexts.$file")["File"];
   				    	  	  $dizin=opendir($core->getDataFolder());
   				    	  	  $dosya=fopen($core->getDataFolder()."$ad", "r");
   				    	  	  $yazi=fread($dosya, filesize($core->getDataFolder()."$ad"));
   				    	  	  fclose($dosya);
   				    	  	  $x=$s->x;
   				    	  	  $y=$s->y - 1;
   				    	  	  $z=$s->z;
   				    	  	  $skin=$s->getSkinData();
   				    	  	  $skinId=$s->getSkinId();
   				    	  	  $yaw=$s->yaw;
   				    	  	  $pitch=$s->pitch;
   				    	  	  $inv=$s->getInventory();
   				    	  	  $main->createLiveText($x, $y, $z, $skin, $skinId, $inv, $yaw, $pitch, $s->chunk, $yazi, $args[1]);
   				    	  	  $s->sendMessage("§6[LiveTexts]§a Text created.");
   				    	  }else{
   				    	  	 $s->sendMessage("§cText not found on texts.yml");
   				    	  }
   				    }else{
   				    	 $s->sendMessage("§eUsage: /lt add <textname>");
   				    }
   				    break;
   		  case "cancel":
   		      if(isset($main->removers[$s->getName()])){
   		      	 unset($main->removers[$s->getName()]);
   		      }
   		      $s->sendMessage("§6[LiveTexts]§e Event cancelled.");
   		      break;
   		  case "remove":
   		      $main->removers[$s->getName()]=true;
   		      $s->sendMessage("§6[LiveTexts]§c Please Touch a LiveText now.");
   		      break;
   	}
   }
  }
 }
?>
