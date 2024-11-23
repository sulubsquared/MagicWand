<?php

namespace MagicWand;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\item\Item;
use pocketmine\entity\effect\Effect;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;

class MagicWand extends PluginBase implements Listener {

    private $potionEffects = [
        "minecraft:speed" => "Speed",
        "minecraft:jump_boost" => "Jump Boost",
        "minecraft:strength" => "Strength",
        "minecraft:regeneration" => "Regeneration",
        "minecraft:poison" => "Poison",
        "minecraft:weakness" => "Weakness",
        "minecraft:invisibility" => "Invisibility",
        "minecraft:night_vision" => "Night Vision"
    ];

    public function onEnable(): void {
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->getLogger()->info("MagicWand plugin enabled!");
    }

    public function onPlayerUseStick(PlayerInteractEvent $event): void {
        $player = $event->getPlayer();
        $item = $event->getItem();

        // Check if right-click with stick
        if ($item->getId() === Item::STICK) {
            $event->setCancelled(true);
            $randomEffect = $this->getRandomPotionEffect();
            $duration = 20 * 5; // 5 seconds duration
            $amplifier = mt_rand(0, 1);

            $this->splashPotion($player, $randomEffect, $duration, $amplifier);
            $player->sendMessage("A random splash potion has been triggered with the Magic Wand!");
        }
    }

    private function getRandomPotionEffect(): string {
        $effectKeys = array_keys($this->potionEffects);
        return $effectKeys[array_rand($effectKeys)];
    }

    private function splashPotion($player, string $effectId, int $duration, int $amplifier): void {
        $effect = Effect::getEffectByName($effectId);
        if ($effect === null) {
            $player->sendMessage("Error: Invalid potion effect.");
            return;
        }

        $effectInstance = new EffectInstance($effect, $duration, $amplifier);
        $player->addEffect($effectInstance);
    }

    // Command handling
    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool {
        if ($command->getName() === "magicwand") {
            // Check if the sender has permission to use the magicwand command
            if (!$sender->hasPermission("magicwand.use")) {
                $sender->sendMessage("You do not have permission to use this command.");
                return false;
            }

            $sender->sendMessage("Use the magic wand by right-clicking with a stick.");
            return true;
        }
        return false;
    }
}
