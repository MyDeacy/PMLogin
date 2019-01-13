<?php
namespace mydeacy\pmlogin;

use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Event;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\utils\TextFormat;

class EventListener implements Listener {

	private $dataCenter;

	public function __construct(DataCenter $dataCenter){
		$this->dataCenter = $dataCenter;
	}

	function onLogin(PlayerJoinEvent $event){
		$player = $event->getPlayer();
		if(!API::isRegister($player)){
			$pass = rand(10000, 99999);
			$player->sendMessage(TextFormat::GREEN."サーバーへようこそ！\n".
								 TextFormat::GREEN."貴方には以下のパスワードが割り当てられました！\n".
								 TextFormat::AQUA." >> ".$pass." <<\n".
								 TextFormat::RED."スクリーンショットなどを撮り、保管しておいてください。");
			API::register($player, $pass);
			return;
		}
		switch(API::checkAccount($player)){
			case API::SUCCESS:
				$player->sendMessage(TextFormat::GREEN." >> 自動認証されました。");
				break;
			case API::SUCCESS_XUID:
				$this->dataCenter->setFlag($player);
				$player->setImmobile(true);
				$player->sendMessage(TextFormat::RED." >> /login {パスワード} コマンドで認証をしてください。");
				break;
			case API::ERROR:
				$player->close("", TextFormat::RED."情報が合致しないためログインできませんでした。");
		}
	}

	function onQuit(PlayerQuitEvent $event){
		$this->dataCenter->removeFlag($event->getPlayer());
	}

	function onBreak(BlockBreakEvent $event){
		$this->onEvent($event);
	}

	function onTap(PlayerInteractEvent $event){
		$this->onEvent($event);
	}

	function onChat(PlayerChatEvent $event){
		$this->onEvent($event);
	}

	private function onEvent(Event $event){
		$player = $event->getPlayer();
		if($this->dataCenter->existFlag($player)){
			$event->setCancelled();
			$player->sendTip(">> /login でログインをしてください <<");
		}
	}
}