<?php
/**
 * Only support .txt files.
 */
namespace EmreTr1;
use pocketmine\plugin\PluginBase;
use pocketmine\command\{Command,CommandSender};
use pocketmine\event\Listener;
use pocketmine\event\entity\{EntityDamageEvent, EntityDamageByEntityEvent};
use pocketmine\nbt\tag\{CompoundTag, ListTag, DoubleTag, FloatTag, StringTag};
use pocketmine\Player;
use pocketmine\Server;
use pocketmine\entity\Entity;
class LiveTexts extends PluginBase implements Listener{
	
	public $cache = [], $whatid = [];
	public $prefix = "§7» §bLive§3Text §7> ";
	
	public function onEnable(){
		$this->scanTextFiles($this->getDataFolder());
		Entity::registerEntity(Text::class, true);
		$this->getServer()->getPluginManager()->registerEvents($this,$this);
	}
	
	public function scanTextFiles(string $dir){
		if(is_dir($dir)){
			$files = scandir($dir);
			
			foreach($files as $file){
				$name = $file;
				$data = trim(str_ireplace("\r\n", "\n", file_get_contents($dir . $file)));
				
				$this->cache[$name] = $data;
			}
		}
	}
	
	public function onCommand(CommandSender $p, Command $cmd, string $label, array $args): bool{
if ($p instanceof Player) {
            if (strtolower($cmd->getName()) === "lt") {
                if (empty($args)) {
                    if(!isset($args[0])) {
                        $p->sendMessage($this->prefix."§aPlease use: §b/lt <subCommand>");
				      return true;
                    }
				    if ($args[0] == "addtext") {
				    array_shift($args);
				    $text = implode(" ", $args);
				    $this->addLiveText($p, $text);
				    $p->sendMessage($this->prefix."§aLiveText created(without file)");
				    return true;
				 }
				 if ($args[0] == "add") {
				    if(!empty($args[1])){
				    	$name = $args[1];
				    	if(isset($this->cache[$name])){
				    		$text = $this->cache[$name];
				    		$this->addLiveText($p, $text);
				    		$p->sendMessage($this->prefix. "§aLiveText created(with file)");
						return true;
				    	}else{
				    		$p->sendMessage($this->prefix."§7{$name} file not found!");
						return true;
				    	}
				    }else{
				    	$p->sendMessage($this->prefix."§7/lt add <filename>");
					return true;
				}
				    
				if ($args[0] == "id") {
				    $this->whatid[$p->getName()] = true;
				    $p->sendMessage($this->prefix."§eTap a entity for known id");
				    return true;
				}
				if ($args[0] == "remove") {
				    if(!empty($args[1])){
				    	$id = $args[1];
				    	foreach($p->level->getEntities() as $e){
				    		if($e->getId() == $id){
				    			$e->close();
				    		}
				    	}
				    	$p->sendMessage($this->prefix."§aText removed.");
				    }
				    }
			}
			return true;
				 }
                    }
                }
				 return true;
}
}
