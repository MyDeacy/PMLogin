<?php
namespace mydeacy\pmlogin;

use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\Player;
use pocketmine\Server;

class API {

	const SUCCESS = 0;

	const SUCCESS_XUID = 1;

	const ERROR = 2;

	public static function register(Player $player, int $pass): int{
		$nbt = $player->namedtag;
		$nbt->setTag(new CompoundTag("PMLogin",
			[
				new StringTag("ip", $player->getAddress()),
				new StringTag("xuid", $player->getXuid()),
				new IntTag("pass", $pass)
			]));
		return $pass;
	}

	public static function changeAddress(Player $player): void{
		$nbt = $player->namedtag->getCompoundTag("PMLogin");
		if(self::isRegister($player))
			$nbt->setString("ip", $player->getAddress());
	}

	public static function deleteUser(String $name): bool{
		$server = Server::getInstance();
		if($server->getPlayer($name) === null){
			if(!file_exists($server->getDataPath()."players/".strtolower($name).".dat"))
				return false;
			$nbt = Server::getInstance()->getOfflinePlayerData($name);
			if($nbt->getCompoundTag("PMLogin") === null)
				return false;
			$nbt->removeTag("PMLogin");
			$server->saveofflinePlayerData($name, $nbt);
		}else{
			$player = $server->getPlayer($name);
			$nbt = $player->namedtag;
			$nbt->removeTag("PMLogin");
			$player->close("", "データが削除されました。再度ログインしてください。");
		}
		return true;
	}

	public static function isRegister(Player $player): bool{
		return $player->namedtag->getCompoundTag("PMLogin") !== null;
	}

	public static function checkAccount(Player $player): int{
		$nbt = $player->namedtag->getCompoundTag("PMLogin");
		$ip = $player->getAddress() === $nbt->getString("ip");
		$xuid = $player->getXuid() === $nbt->getString("xuid");
		if($ip && $xuid){
			return self::SUCCESS;
		}elseif($xuid){
			return self::SUCCESS_XUID;
		}
		return self::ERROR;
	}

	public static function checkPass(Player $player, int $pass): bool{
		$nbt = $player->namedtag->getCompoundTag("PMLogin");
		return $pass === $nbt->getint("pass");
	}
}