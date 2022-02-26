<?php

namespace palente\luckyblock;

use palente\luckyblock\events\BlockBreak;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;

//use palente\luckyblock\events\BlockPlace;

class Main extends PluginBase {
    //TODO: Make that better
	/** @var Main $main */
	private static Main $main;
    private static Api $api;
    private static Config $config;

	/** @var ?object $economyPlugin and $mode_eco economyAPI plugin variables */
	public ?object $economyPlugin = null;

	/** @var ?object $piggyPlugin and $mode_enc PiggyCustomEnchant plugin variables */
	public ?object $piggyPlugin = null;

	/** @var string $prefix  prefix */
	public const PREFIX = "§e[§bLuckyBlock§e]§r" . " ";

    /** @var ?object $bedrockEconomy */
    public ?object $bedrockEconomy = null;
	/**
	 * When the plugin is started.
	 * @return void
	 */
	public function onEnable() : void {
		# Register all plugin's events:
		$this->registerEvents();

		# Creating the configuration if it is not done and updating it:
		if(file_exists($this->getDataFolder() . "config.yml")){
			$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

			if(!$config->exists("version") || $config->get("version") !== $this->getDescription()->getVersion()){
				$this->getLogger()->warning("Critical changes have been made in the new version of the plugin and it seem that your config.yml is a older config.");
				$this->getLogger()->warning("Your config has been updated, be careful to check the content change !");
				$this->getLogger()->warning("You can find your old config in oldConfig.yml file.");

				rename($this->getDataFolder() . "config.yml", $this->getDataFolder() . "oldConfig.yml");
				$this->saveResource("config.yml", true);
			}
		} else {
			$this->saveResource("config.yml");
		}

		# Register statics:
		self::$main = $this;
		self::$api = new Api();
		self::$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);

		# Enabling the use of the EconomyAPI plugin:
		if(self::getDefaultConfig()->get("usage-of-EconomyAPI") === true){
			if($this->getServer()->getPluginManager()->getPlugin("EconomyAPI")){
				$this->economyPlugin = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
			} else {
				$this->getLogger()->error("You have enabled the usage of the plugin EconomyAPI but the plugin is not found.");
			}
		}

		# Enabling the use of the PiggyCustomEnchant plugin:
		if(self::getDefaultConfig()->get("usage-of-PiggyCustomEnchants") === true){
			if($this->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants")){
				$this->piggyPlugin = $this->getServer()->getPluginManager()->getPlugin("PiggyCustomEnchants");
			} else {
				$this->getLogger()->error("You have enabled the usage of the plugin PiggyCustomEnchants but the plugin is not found.");
			}
		}
        # Enabling the use of the BedrockEconomy plugin:
        if(self::getDefaultConfig()->get("usage-of-BedrockEconomy") === true){
            if($this->getServer()->getPluginManager()->getPlugin("BedrockEconomy")){
                $this->bedrockEconomy = $this->getServer()->getPluginManager()->getPlugin("BedrockEconomy");
            } else {
                $this->getLogger()->error("You have enabled the usage of the plugin BedrockEconomy but the plugin is not found.");
            }
        }
	}

	/**
	 * Register all plugin's events.
	 * @return void
	 */
	public function registerEvents() : void {
        //registering useless listener
		$events = array(new BlockBreak()/*, new BlockPlace()*/);

		foreach($events as $event){
			$this->getServer()->getPluginManager()->registerEvents($event, $this);
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