<?php

/**
 * LiveText Event
 *
 * When player tap the livetext call this event
 */
 
namespace EmreTr1\Events;

use pocketmine\event\Event;
use pocketmine\Player;

use EmreTr1\Text;

class TapLiveTextEvent extends Event{
	
	const CAUSE_TAP = 0;
	const CAUSE_REMOVE = 1;
	const CAUSE_ID = 2;
	
	/** @var null */
	public static $handlerList = null;
	/** @var Player */
	protected $player;
	/** @var Text */
	protected $text;
	
	/** 
	 * @param Player $player
	 * @param Text $text
	 */
	public function __construct(Player $player, Text $text, int $cause = 0){
		$this->player = $player;
		$this->text = $text;
		$this->cause = $cause;
	}
	
	/**
	 * @return Player
	 */
	public function getPlayer(){
		return $this->player;
	}
	
	/**
	 * @return Text
	 */
	public function getText(){
		return $this->text;
	}
	
	/** 
	 * @return Text
	 */
	public function getLiveText(){
		return $this->text;
	}
	
	/**
	 * @return int
	 */
	public function getCause(){
		return $this->cause;
	}
}
?>