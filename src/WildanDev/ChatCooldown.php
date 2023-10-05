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
        $this->saveDefaultConfig(); // Ini akan membuat config.yml jika belum ada
        $this->reloadConfig(); // Ini akan memuat konfigurasi dari berkas config.yml

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
        
        $config = $this->getConfig(); // Mendapatkan konfigurasi dari config.yml
        $cooldownTime = (int) $config->get("cooldown_time", 3); // Mendapatkan waktu cooldown dari konfigurasi (default 3 detik)

        if (isset($this->chatCooldowns[$playerName]) && $this->chatCooldowns[$playerName] > $currentTime) {
            $remainingTime = $this->chatCooldowns[$playerName] - $currentTime;
            $cooldownMessage = str_replace("{remaining_time}", $remainingTime, $config->get("cooldown_message", "&cYou are on cooldown. Please wait for {remaining_time} seconds before chatting again."));
            $player->sendMessage(TextFormat::colorize($cooldownMessage));
            $event->cancel();
        } else {
            // Set a new cooldown time
            $this->chatCooldowns[$playerName] = $currentTime + $cooldownTime;
        }
    }
}
