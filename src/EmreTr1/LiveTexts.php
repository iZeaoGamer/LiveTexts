<?php
   /**
    * LiveTexts Plugin for Pocketmine(and all forks)
    *
    * Supported version of mcpe: 1.0.0
    *
    * Add floating text on the your Server
    *
    * @author EmreTr1
    */
   
   namespace EmreTr1;
   
   use pocketmine\plugin\PluginBase;
   use pocketmine\Player;
   use pocketmine\Server;
   use pocketmine\entity\Entity;
   use pocketmine\entity\Human;
   use pocketmine\math\Vector3;
   use pocketmine\event\Listener;
   use pocketmine\command\Command;
   use pocketmine\command\CommandSender;
   use pocketmine\event\entity\EntityDamageEvent;
   use pocketmine\event\entity\EntityDamageByEntityEvent;
   use pocketmine\nbt\tag\CompoundTag;
   use pocketmine\utils\Config;
   use pocketmine\nbt\tag\ByteTag;
   use pocketmine\nbt\tag\ListTag;
   use pocketmine\nbt\tag\DoubleTag;
   use pocketmine\nbt\tag\FloatTag;
   use pocketmine\nbt\tag\ShortTag;
   use pocketmine\nbt\tag\StringTag;
         
   class LiveTexts extends PluginBase implements Listener{
   	
   	public $config;
   	public $removers = [];
   	public $iders = []; // :D
   	private static $instance;
   	
   	/**
   	 * Get this class functions, variables from a other Class
   	 */
   	public static function getInstance(){
   		return self::$instance;
   	}
   	
   	/**
   	 * On Plugin Enable
   	 */
   	public function onEnable(){
   		self::$instance=$this;
   		$this->getServer()->getPluginManager()->registerEvents($this, $this);
   		Server::getInstance()->getLogger()->info("§dLiveTexts §eStarting...");
   		Server::getInstance()->getCommandMap()->register("FloatingText", new Livecommands("lt"));
   		Entity::registerEntity(Text::class, true);
   		$this->loadConfig();
   	}
   	
   	/**
   	 * When Config loading
   	 */
   	public function loadConfig(){
   		$main=LiveTexts::getInstance();
   		@mkdir($this->getDataFolder());
   		$this->config=new Config($this->getDataFolder() . "texts.yml", Config::YAML);
    		if(!$this->config->get("LiveTexts")){
   			$opt=[
   			"File"=>"welcome.txt",
   			'Permissions' => []];
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
   	
   	/** @param Text $text */
   	public function createFileFor(Text $text){
   		/*if($text->hasFile()){
   			return false;
   		}                           TODO
   	 $main = LiveTexts::getInstance();
   		$name = 
   		touch($main->getDataFolder().'');*/
   	}
   	
   	/** @param Text $text */
   	public function removeFileFrom(Text $text){}
   	
   	/**
   	 * On Damage the LiveText
   	 *
   	 * @param EntityDamageEvent $event
   	 */
   	public function onDamage(EntityDamageEvent $event){
   		$entity=$event->getEntity();
   		$main=LiveTexts::getInstance();
   		if($event instanceof EntityDamageByEntityEvent){
   			$damager=$event->getDamager();
   			if($damager instanceof Player){
   				if($entity instanceof Text){ 
   					 if(isset($main->removers[$damager->getName()])){
   		  	   $entity->close();
   		  	   $damager->sendMessage("§6[LiveTexts]§c LiveText removed.");
   		  	   unset($main->removers[$damager->getName()]);
   		  	  }elseif(isset($main->iders[$p->getName()])){
   		  	  	$id = $entity->getId();
   		  	  	$damager->sendMessage("§6[LiveTexts] §bEntity ID: $id");
   		  	  	unset($main->iders[$p->getName()]);
   		  	  }
   		   }
   		  }
   		}
   	}
   	
   	/**
   	 * Replace the text(for all players)
   	 *
   	 * @param string $text
   	 */
   	public function replacedText(string $text){ 
   		$tps=$this->getServer()->getTicksPerSecond();
   		$onlines=count($this->getServer()->getOnlinePlayers());
   		$maxplayers=$this->getServer()->getMaxPlayers();
   		$worldsc=count($this->getServer()->getLevels());
   		$server=$this->getServer();
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
   	
   	/**
   	 * Replace the text for Player(show)
   	 *
   	 * @param Player $p
   	 * @param string $text
   	 */
   	public function replaceForPlayer(Player $p, string $text){
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
   	
   	/**
   	 * Add a LiveText with a invisible Human
   	 *
   	 * @param int $x, $y, $z, $skinId
   	 * @param mixed $skin
   	 * @param object $inv, $chunk
   	 * @param float $yaw, $pitch
   	 * @param string $tag, $name, $file
   	 */
   	public function createLiveText($x, $y, $z, $skin, $skinId, $inv, $yaw, $pitch, $chunk, $tag, $name, $file=""){
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
   	 $nbt->infos=new ListTag("infos", ["file"=>$file, "datafolder"=>$this->getDataFolder()."$name"]);
   	 $entity=Entity::createEntity("Text", $chunk, $nbt, $tag);
   	 $entity->spawnToAll();
   	}
   }
   
   class Livecommands extends Command{
   	
   	private $name;
   	
   	/** special function */
   	public function __construct($name){
   		parent::__construct(
   		$name,
   		"LiveTexts plugin main Command",
   		"/lt <add|cancel|remove>");
   		$this->setPermission("livetext.command.use");
   	}
   	
   	/**
   	 * When command running
   	 *
   	 * @param CommandSender $s
   	 * @param string $label
   	 * @param array $args
   	 */
   	public function execute(CommandSender $s, $label, array $args){
    if(!$s->hasPermission("livetext.command.use")){
      return true;
    }
    $help="§aLive§bTexts §6Help Page\n
    §7- /lt addtext <text(unlimited args)> :§e Add livetext without file. You can use {line} for new line\n
    §7- /lt add <TextName> :§e Add livetext with file\n
    §7- /lt remove :§e Remove a LiveText when Tap a entity\n
    §7- /lt updateall :§e Update All old LiveTexts to New LiveTexts\n
    §7- /lt cancel :§e Cancel remove event\n
    §7- /lt id:§e See the Text ID\n
    §7- /lt edit <args>:§e Edit the Text";
   		if(!empty($args[0])){
   			$main=LiveTexts::getInstance();
   			$core=LiveTexts::getInstance();
   			$pre = "§6[LiveTexts]§b ";
   			switch($args[0]){
   				case 'edit':
   				    if(!empty($args[1])){
   				    	$entity = $s->getLevel()->getEntity((int) $args[1]);
   				    	if($entity instanceof Text){
   				    		switch($args[2]){
   				    			case 'text':
   				    			case 'string':
   				    			case 'str':
   				    			case 'tag':
   				    			case 'name':
   				    			    if(empty($args[3])){
   				    			    	return $s->sendMessage($pre.'Usage: /lt edit <eid> text <string>');
   				    			    }
   				    			    if($entity->hasFile()){
   				    			    	$s->sendMessage($pre.'This LiveText has a file so you can\'t edit this text\nFirst, execute the command /lt edit <eid> format <format> if you want change this text name');
   				    			    	return;
   				    			    }
   				    			    array_shift($args);
   				    			    array_shift($args);
   				    			    array_shift($args);
   				    			    $entity->setNameTag($main->replacedText(trim(implode(" ", $args))));
   				    			    $s->sendMessage($pre.'Text changed');
   				    			    break;
   				    			case 'format':
   				    			case 'type':
   				    			case 'style':
   				    			    if(!empty($args[3])){
   				    			    	if($args[3] == 'file'){
   				    			    		if(!$entity->hasFile()){
   				    			    			$main->createFileFor($entity);
   				    			    		 $s->sendMessage($pre.'Format changed to File');
   				    			    		}
   				    			    	}elseif($args[3] == 'tag'){
   				    			    		if($entity->hasFile()){
   				    			    			$main->removeFileFrom($entity);
   				    			    			$s->sendMessage($pre.'Format changed to Tag');
   				    			    		}
   				    			    	}else{
   				    			    		$s->sendMessage($pre.'Format not found. (must be file or tag)');
   				    			    	}
   				    			    }else{
   				    			    	$s->sendMessage($pre.'Usage: /lt edit <eid> format <file|tag>');
   				    			    }
   				    			    break;
   				    			case 'tp':
   				    			case 'teleport':
   				    			    if(!empty($args[3])){
   				    			    	@list($x, $y, $z) = explode(":", $args[3]);
   				    			    	$tp = null;
   				    			    	if($p = $main->getServer()->getPlayer($x) !== false){
   				    			    		$tp = $p;
   				    			    	}else{
   				    			    		$tp = new Vector3((int) $x, (int) $y, (int) $z);
   				    			    	}
   				    			    	if($tp != null){
   				    			    		$entity->teleport($tp);
   				    			    		$s->sendMessage($pre.'LiveText teleported to {$tp}');
   				    			    	}else{
   				    			    		$s->sendMessage($pre.'Unexpected a error.');
   				    			    	}
   				    			    }else{
   				    			    	$s->sendMessgae($pre.'Usage: /lt edit <eid> tp <x:y:z|PlayerName>');
   				    			    }
   				    			    break;
   				    			case 'addperm':
   				    			case 'addpermission':
   				    			    if(!empty($args[3])){
   				    			    	if(!isset($entity->namedtag->Permissions)){
   				    			    		$entity->namedtag->Permissions = new ListTag("Permissions", []);
   				    			    	}
   				    			    	$entity->namedtag->Permissions->{trim($args[3])} = new StringTag((string) $args[3], (string) $args[3]);
   				    			    	$s->sendMessage("§6[LiveTexts] §a{$args[3]} permission added!");
   				    			    }
   				    			    break;
   				    			case 'delperm':
   				    			case 'removepermission':
   				    			    if(!empty($args[3])){
   				    			    	if(!isset($entity->namedtag->Permissions) or !isset($entity->namedtag->Permissions->{$args[3]})){
   				    			    	 $s->sendMessage("§6[LiveTexts]§c {$args[3]} permission not found!");
   				    			    	}
   				    			    	unset($entity->namedtag->Permissions->{trim($args[3])});
   				    			    	
   				    			    	$s->sendMessage("§6[LiveTexts] §c{$args[3]} permission removed!");
   				    			    }
   				    			    break;
                           case 'save':
                              //TODO: save the livetexts to a data folder
                              break;
   				    		}
   				    	}
   				    }else{
   				    	$s->sendMessage("§aLiveTexts Edit commands:\n§etext: /lt edit <eid> text <Text>\nformat: /lt edit <eid> format <file|tag>\ntp: /lt edit <eid> tp <x:y:z|PlayerName>\naddperm: /lt edit <eid> addperm <permission>\ndelperm: /lt edit <eid> delperm <permission>");
   				    }
   				    break;
   				case "addtext":
   				    array_shift($args);
   				    $text = implode(" ", $args);
   				    $replaced=$main->replacedText($text);
   				    $main->createLiveText($s->x, $s->y - 1, $s->z, $s->getSkinData(), $s->getSkinId(), $s->getInventory(), $s->yaw, $s->pitch, $s->chunk, $replaced, $args[0]);
   				    $s->sendMessage("§6[LiveTexts] §eLiveText created(not file)");
   				    break;
   				case "add":
   				    if(!empty($args[1])){
   				    	  $file=$args[1];
   				    	  if($main->config->getNested("LiveTexts.$file")){
   				    	  	  $ad=$main->config->getNested("LiveTexts.$file")["File"];
   				    	  	  $yazi = file_get_contents($main->getDataFolder()."$ad");
   				    	  	  $x=$s->x;
   				    	  	  $y=$s->y;
   				    	  	  $z=$s->z;
   				    	  	  $skin=$s->getSkinData();
   				    	  	  $skinId=$s->getSkinId();
   				    	  	  $yaw=$s->yaw;
   				    	  	  $pitch=$s->pitch;
   				    	  	  $inv=$s->getInventory();
   				    	  	  $main->createLiveText($x, $y, $z, $skin, $skinId, $inv, $yaw, $pitch, $s->chunk, $yazi, $args[1], $yazi);
   				    	  	  $s->sendMessage("§6[LiveTexts]§a Text created.");
   				    	  }else{
   				    	  	 $s->sendMessage("§6[LiveTexts] §cText not found on texts.yml");
   				    	  }
   				    }else{
   				    	 $s->sendMessage("§eUsage: /lt add <textname>");
   				    }
   				    break;
   				case "updateall":
   				    $levels=$main->getServer()->getLevels();
   				    foreach($levels as $level){
   				    	$entities=$level->getEntities();
   				    	foreach($entities as $entity){
   				    		if(isset($entity->namedtag->LiveTextName)){
   				    			if(!isset($entity->namedtag->infos) or (!$entity instanceof Human)){
   				    				$ad=$entity->namedtag->LiveTextName;
   				    				$yazi = file_get_contents($main->getDataFolder()."$ad");
   				    				$main->createLiveText($entity->x, $entity->y + 1, $entity->z, $entity->getSkinData(), $entity->getSkinId(), $entity->getInventory(), $entity->yaw, $entity->pitch, $entity->chunk, $entity->namedtag->CustomName, "$ad", $yazi);
   				    				$entity->close();
   				    			}
   				    		}
   				    	}
   				    }
   				    $s->sendMessage("§6[LiveTexts]§a All old LiveTexts has been updated!");
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
   		  case 'id':
   		      $main->iders[$s->getName()] = true;
   		      $s->sendMessage("§6[LiveTexts]§a Tap a entity for see id");
   		      break;
     	}
   }else{
   	 $s->sendMessage($help);
   }
  }
 }
?>
