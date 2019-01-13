<?php
namespace mydeacy\pmlogin;

 use pocketmine\Player;

 class DataCenter {

	private $notLogin = [];

	 public function existFlag(Player $player): bool{
		 return isset($this->notLogin[$player->getName()]);
	 }

	 public function setFlag(Player $player): void{
	 	$this->notLogin[$player->getName()] = true;
	 }

	 public function removeFlag(Player $player): void{
	 	$name = $player->getName();
		 if(isset($this->notLogin[$name])){
			 unset($this->notLogin[$name]);
		 }
	 }
 }