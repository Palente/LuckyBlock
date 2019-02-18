# LuckyBlock
LuckyBlock is a free plugin for PMMP in PHP. The plugins depends on [EconomyAPI](https://poggit.pmmp.io/p/EconomyAPI/5.7.2) and can depends on [PiggyCustomEnchants](https://poggit.pmmp.io/p/PiggyCustomEnchants/1.4.2)
## Installation
To install the plugin download the .phar in poggit (LATER URL),
Now place it in your plugins folder and start the server and stop it.
Go to your plugins_data folder and in the LuckyBlock folder read the config.yml edit it, start your server!
## Usage Config
For choose a drop you have to select one type of them:
 - items (2 arguments) :
 	- iditems (Item's id)
 	- amountItems (Item's amount)
 - block (1 argument) :
 	- idBlocks (Block's id)
 - money (1 argument) **NEED ECONOMYAPI**:
 	- moneyToAdd (the amount of money that the player will receive)
 	- amountItems (Item's amount)
 - commands (2 arguments) :
 	- command (The command to execute without "/")
 	- executor (The command have to be executed by who (console or player))
 - enchant (4 arguments) **NEED PIGGYCUSTOMENCHANT**:
 	- iditems (Item's id)
 	- amountItems (Item's amount)
 	- enchantName (The id or the name of the enchant to give)
 	- enchantLevel (the enchant's level)
 - null (no gain)
Exemple:
```YAML
Chance-0: 
  Type: items
  idItems: id
  amountItems: amount
Chance-2:
  Type: blocks
  idBlocks: id
Chance-3:
  Type: money
  moneyToAdd: moneytogive
Chance-4:
  Type: null
Chance-5:
  Type: Commands
  command: the command without the "/"
  executor: the executor
Chance-6:
  Type: enchant
  idItems: id
  amountItems: amount
  enchantName: id enchant
  enchantLevel: level enchant

```
## Any Suggestion or Future update
Feel free to open an issue
### Future Update
 - [x] Support CustomEnchants [Issue **#1**](https://github.com/Palente/LuckyBlock/issues/1)
 - [ ] Possibility to execute more than 1commands
 - [ ] Possibility to drop more items
 - [ ] Custom message
 - [ ] Rewrite config.yml -See future update(critical changes of config)

### Icon image
[MINECRAFT GAMEPEDIA](https://minecraft.gamepedia.com/Mods/Lucky_Block)