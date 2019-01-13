<?php
namespace mydeacy\pmlogin;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\Player;
use pocketmine\utils\TextFormat;

class CommandProcessor {

	private $dataCenter;

	public function __construct(DataCenter $dataCenter){
		$this->dataCenter = $dataCenter;
	}

	public function onCommand(CommandSender $sender, Command $command, array $args): bool{
		if(!isset($args[0]))
			return false;
		switch(strtolower($command->getName())){
			case "login":
				if(!$sender instanceof Player){
					$sender->sendMessage(TextFormat::RED.">> プレイヤー以外から使用できません。");
					return true;
				}
				if(!$this->dataCenter->existFlag($sender)){
					$sender->sendMessage(TextFormat::AQUA.">> あなたは既にログイン済みです。");
					return true;
				}
				if(API::checkPass($sender, (int)$args[0])){
					API::changeAddress($sender);
					$sender->setImmobile(false);
					$this->dataCenter->removeFlag($sender);
					$sender->sendMessage(TextFormat::GREEN.">> ログインに成功しました！");
					return true;
				}
				$sender->close("",TextFormat::RED."認証に失敗したためログアウトされました。");
				break;
			case "del":
				if(API::deleteUser($args[0])){
					$sender->sendMessage(TextFormat::GREEN.">> ".$args[0]."のデータを削除しました。");
				}else{
					$sender->sendMessage(TextFormat::RED.">> ".$args[0]."のデータが見つかりませんでした");
				}
		}
		return true;
	}
}