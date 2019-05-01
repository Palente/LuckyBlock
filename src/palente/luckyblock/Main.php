<?php

namespace palente\luckyblock;

use pocketmine\plugin\PluginBase;

use pocketmine\Player;

use pocketmine\Server;

use pocketmine\utils\Config;

use palente\luckyblock\Events;
use palente\luckyblock\Api;

class Main extends PluginBase {

	/** @var $main, $api and $config instances */
    private static $main, $api, $config;

	/** @var $economyPlugin and $mode_eco economyAPI plugin variables */
	public $economyPlugin;

	/** @var $piggyPlugin and $mode_enc PiggyCustomEnchant plugin variables */
	public $piggyPlugin;

	/** @var $prefix the prefix */
	const PREFIX = "§e[§bLuckyBlock§e]§r" . " ";

	/**
	 * When the plugin is started.
	 */
	public function onEnable(){
		# Register events class:
		$this->getServer()->getPluginManager()->registerEvents(new Events(), $this);

		# Creating the configuration if it is not done and updating it:
		if(file_exists($this->getDataFolder() . "config.yml")){
			$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

            if($config->get("version") != $this->getDescription()->getVersion() or !$config->exists("version")){
				$this->getLogger()->warning("Critical changes have been made in the new version of the plugin and it seem that your config.yml is a older config.");
				$this->getLogger()->warning("Your config has been updated, be careful to check the content change !");
				$this->getLogger()->warning("You can find your old config in OldConfig.yml file.");

				rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "oldConfig.yml");
				$this->saveResource("config.yml", true);
			}
		} else {
			$this->getLogger()->info("The LuckyBlock config as been created !");
			$this->saveResource("config.yml");
		}

		# Register statics:
		self::$main = $this;
		self::$api = new Api();
		self::$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		
		# Enabling the use of the EconomyAPI plugin:
		if(self::getDefaultConfig()->get("usage_of_EconomyAPI") == "true"){
			if($this->getServer()->getPluginManager()->getPlugin("EconomyAPI")){
				$this->economyPlugin = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
			} else {
				$this->getLogger()->error("You have enabled the usage of the plugin EconomyAPI but the plugin is not found.");
			}
		}

		# Enabling the use of the PiggyCustomEnchant plugin:
		if(self::getDefaultConfig()->get("usage_of_PiggyCustomEnchants") == "true"){
			if($this->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants")){
				$this->piggyPlugin = $this->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants");
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
	 * Return instance of Api class.
	 * @return Api
	 */
	public static function getApi() : Api {
		return self::$api;
	}

	/**
     * Return instance of plugin config.
     * @return Config
     */
    public static function getDefaultConfig() : Config {
        return self::$config;
	}
}