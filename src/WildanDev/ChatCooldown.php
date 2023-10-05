<?php

declare(strict_types=1);

namespace WildanDev;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class ChatCooldown extends PluginBase implements Listener {
    
    private $chatCooldowns = [];

    public function onEnable(): void {
        $this->getLogger()->info("ChatCooldown plugin enabled!");
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function onPlayerChat(PlayerChatEvent $event): void {
        $player = $event->getPlayer();
        
        // Check if the player has the bypass permission
        if ($player->hasPermission("chatcooldown.bypass")) {
            return; // Bypass chat cooldown
        }
        
        $playerName = $player->getName();
        $currentTime = time();
        
        if (isset($this->chatCooldowns[$playerName]) && $this->chatCooldowns[$playerName] > $currentTime) {
            $remainingTime = $this->chatCooldowns[$playerName] - $currentTime;
            $player->sendMessage(TextFormat::RED . "You are on cooldown. Please wait for " . $remainingTime . " seconds before chatting again.");
            $event->cancel();
        } else {
            // Set a new cooldown time (e.g., 10 seconds) for the player
            $this->chatCooldowns[$playerName] = $currentTime + 3;
        }
    }
}
