<?php

namespace Palente\LuckyBlock;

use pocketmine\plugin\PluginBase;

use pocketmine\Player;

use pocketmine\Server;
use pocketmine\utils\TextFormat as TX;

use pocketmine\utils\Config;

use Palente\LuckyBlock\utils\Events;

class Main extends PluginBase {

	/** @var $main and $config instances */
    public static $main, $config;

	public $economyPlugin;
	public $mode_eco = false;

	public $piggyPlugin;
	public $mode_enc = false;

	public $prefix = TX::BLUE."[".TX::AQUA."LuckyBlock".TX::BLUE."] ".TX::RESET;

	/**
	 * When the plugin is started.
	 */
	public function onEnable(){
		# Register events class:
		$this->getServer()->getPluginManager()->registerEvents(new Events(), $this);

		# Creating the configuration if it is not done and updating it:
		if(file_exists($this->getDataFolder() . "config.yml")){
            if(self::getDefaultConfig()->get("version") !== $this->getVersion()){
				$this->getLogger()->warning("Critical changes have been made in the new version of the plugin and it seem that your config.yml is a older config. Please delete your config.yml and restart your server.");
				//TODO: ideas for the method used to update the configuration ?
			}
		} else {
			$this->saveResource("config.yml");
		}
		
		# Register statics:
		self::$main = $this;
		self::$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

		# Enabling the use of the EconomyAPI plugin:
		if(self::getDefaultConfig()->get("usage_of_EconomyAPI") == "true"){
			if($this->getServer()->getPluginManager()->getPlugin("EconomyAPI")){
				$this->economyPlugin = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
				$this->mode_eco = true;
			} else {
				$this->getLogger()->error("You have enabled the usage of the plugin EconomyAPI but the plugin is not found.");
			}
		}

		# Enabling the use of the PiggyCustomEnchant plugin:
		if(self::getDefaultConfig()->get("usage_of_PiggyCustomEnchants") == "true"){
			if($this->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants")){
				$this->piggyPlugin = $this->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants");
				$this->mode_enc = true;
			} else {
				$this->getLogger()->error("You have enabled the usage of the plugin PiggyCustomEnchants but the plugin is not found.");
			}
		}
	}

	/**
	 * Return instance of Main class.
	 * @return Main
	 */
	public static function getInstance() : Main {
		return self::$main;
	}

	/**
     * Return instance of plugin config.
     * @return Config
     */
    public static function getDefaultConfig() : Config {
        return self::$config;
    }
}