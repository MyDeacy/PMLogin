<?php
namespace mydeacy\pmlogin;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\plugin\PluginBase;

class PMLogin extends PluginBase {

	/**@var \mydeacy\pmlogin\CommandProcessor*/
	private $processor;

	public function onEnable(){
		$dataCenter = new DataCenter();
		$listener = new EventListener($dataCenter);
		$this->getServer()->getPluginManager()->registerEvents($listener, $this);
		$this->processor = new CommandProcessor($dataCenter);
	}

	public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
		return $this->processor->onCommand($sender, $command, $args);
	}

}
