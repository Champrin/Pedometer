<?php
namespace CaiBin\PM\Finally_Program\Pedometer;

use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat as C;
use pocketmine\utils\config;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\Player;
use pocketmine\event\player\PlayerJoinEvent;

class Pedometer extends PluginBase implements Listener
{
    public $tip;
    public $Step=0;
    public function onEnable()
    {
        @mkdir($this->getDataFolder(),0777,\true);
        $this->tip = new Config($this->getDataFolder() . "PlayerIn.yml", Config::YAML, array());
        $this->getServer()->getPluginManager()->registerEvents($this,$this);
        $this->getLogger()->info(C::GOLD."Spiderman[CaiBin]***********PM弃坑之作");
        $this->getLogger()->info(C::GOLD."Pedometer---计步器插件已加载");
    }
    public function getStep($name)
    {
        return $this->tip->get($name)["Step_number"];
    }
    public function getPos($name)
    {
        return $this->tip->get($name)["Position"];
    }
    public function setStep($name,$num)
    {
        $this->tip->set($name,[
            "Step_number"=>$num,
            "Position"=>$this->tip->get($name)["Position"]
        ]);
        $this->tip->save();
    }
    public function setPos($name,$pos)
    {
        $this->tip->set($name,[
            "Step_number"=>$this->tip->get($name)["Step_number"],
            "Position"=>$pos
        ]);
        $this->tip->save();
    }
    public function onjoin(PlayerJoinEvent $event)
    {
        $player = $event->getPlayer();
        $name = $player->getName();
        $x = floor($player->getX());
        $y = floor($player->getY());
        $z = floor($player->getZ());
        $pos = "{$x}:{$y}:{$z}";
        if(!$this->tip->exists($name))
        {
            $this->tip->set($name,[
                "Step_number"=>$this->Step,
                "Position"=>"0:0:0"
            ]);
            $this->tip->save();
        }
        else
        {
            $this->setPos($name,$pos);
            unset($x,$y,$z,$pos);
        }
    }
    public function onmove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        $name = $player->getName();
        $x = floor($player->getX());
        $y = floor($player->getY());
        $z = floor($player->getZ());
        $pos = $this->getPos($name);
        $step =  $this->getStep($name);
        $player->sendPopup("\n\n\n\n\n步数  ："."$step");/**此处为步数显示**/
        $num = explode(":",$pos);
        if($x != $num[0])
        {
            $a = abs($x-$num[0]);
            $step = $step+$a;
            $this->setStep($name,$step);
        }
        elseif($y != $num[1])
        {
            $a = abs($y-$num[1]);
            $step = $step+$a;
            $this->setStep($name,$step);
        }
        elseif($z != $num[2])
        {
            $a = abs($z-$num[2]);
            $step = $step+$a;
            $this->setStep($name,$step);
        }
        $this->setPos($name,"{$x}:{$y}:{$z}");

        unset($x,$y,$z,$pos,$step,$num);
    }
    public function oTeleport(EntityTeleportEvent $event)
    {
        $player = $event->getEntity();
        if($player instanceof Player)
        {
            $name = $player->getName();
            $x = floor($player->getX());
            $y = floor($player->getY());
            $z = floor($player->getZ());
            $pos = "{$x}:{$y}:{$z}";
            $this->setPos($name,$pos);
            unset($x,$y,$z,$pos);
        }
    }
}