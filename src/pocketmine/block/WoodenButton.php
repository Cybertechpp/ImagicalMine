<?php

/*
 *
 *  _                       _           _ __  __ _             
 * (_)                     (_)         | |  \/  (_)            
 *  _ _ __ ___   __ _  __ _ _  ___ __ _| | \  / |_ _ __   ___  
 * | | '_ ` _ \ / _` |/ _` | |/ __/ _` | | |\/| | | '_ \ / _ \ 
 * | | | | | | | (_| | (_| | | (_| (_| | | |  | | | | | |  __/ 
 * |_|_| |_| |_|\__,_|\__, |_|\___\__,_|_|_|  |_|_|_| |_|\___| 
 *                     __/ |                                   
 *                    |___/                                                                     
 * 
 * This program is a third party build by ImagicalMine.
 * 
 * PocketMine is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Lesser General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * @author ImagicalMine Team
 * @link http://forums.imagicalcorp.ml/
 * 
 *
*/

namespace pocketmine\block;

use pocketmine\item\Item;
use pocketmine\level\Level;
use pocketmine\Player;
use pocketmine\math\Vector3;

class WoodenButton extends Flowable implements Redstone{
	
	protected $id = self::WOODEN_BUTTON;

	public function __construct($meta = 0){
		$this->meta = $meta;
	}

	public function getName(){
		return "Wooden Button";
	}

	public function getHardness(){
		return 0.5;
	}

	public function onUpdate($type){
		/*if($type === Level::BLOCK_UPDATE_NORMAL){
			if($this->getSide($this->getAttachedFace()) instanceof Transparent){
				$this->getLevel()->useBreakOn($this);
				return Level::BLOCK_UPDATE_NORMAL;
			}
		}
		else*//*if($type === Level::BLOCK_UPDATE_SCHEDULED or $type === Level::BLOCK_UPDATE_RANDOM){
			if($this->isPowered()){
				$this->togglePowered();
			}
			$this->getLevel()->setBlock($this, $this, true, true, true);
			return Level::BLOCK_UPDATE_SCHEDULED;
		}*/
		if($type === Level::BLOCK_UPDATE_SCHEDULED){
			$this->togglePowered();
			return Level::BLOCK_UPDATE_SCHEDULED;
		}
		/*elseif($type === Level::BLOCK_UPDATE_TOUCH){
			$this->meta ^= 0x08;
			$this->power=15;
			$this->getLevel()->setBlock($this, $this, $this);
			return Level::BLOCK_UPDATE_NORMAL;
		}*/
		return false;
	}

	public function place(Item $item, Block $block, Block $target, $face, $fx, $fy, $fz, Player $player = null){
		if($target->isTransparent() === false){
			/*$faces = [
				0 => 5,
				1 => 0,
				2 => 3,
				3 => 4,
				4 => 1,
				5 => 2,
			];
			$face+=1;
			if($face>5)
				$face=0;*/
			$faces = [
				0 => 0,
				1 => 5,
				3 => 3,
				2 => 4,
				4 => 2,
				5 => 1,
			];
			$this->meta=$face;
			$this->getLevel()->getServer()->broadcastMessage($this->meta);
			$this->getLevel()->setBlock($block, $this, true, true);
			
			return true;
		}
		
		return false;
	}

	public function onActivate(Item $item, Player $player = null){
		if(($player instanceof Player && !$player->isSneaking())||$player===null){
			$this->getLevel()->scheduleUpdate($this, 500);
			$this->togglePowered();
		}
		$this->getLevel()->getServer()->broadcastMessage($this->meta);
	}

	public function getDrops(Item $item){
		return [[$this->id,0,1]];
	}

	public function isPowered(){
		return (($this->meta & 0x08) === 0x08);
	}

	/**
	 * Toggles the current state of this button
	 *
	 * @param
	 *        	bool
	 *        	whether or not the button is powered
	 */
	public function togglePowered(){
		$this->getLevel()->getServer()->broadcastMessage($this->meta);
		$this->meta ^= 0x08;
		$this->isPowered()?$this->power=15:$this->power=0;
		$this->getLevel()->getServer()->broadcastMessage($this->meta);
		$this->getLevel()->setBlock($this, $this ,true ,true);
	}

	/**
	 * Gets the face that this block is attached on
	 *
	 * @return BlockFace attached to
	 */
	public function getAttachedFace(){
		$data = $this->meta;
		if($this->meta & 0x08 === 0x08) // remove power byte if powered
			$data |= 0x08;
		$faces = [
				5 => 0,
				0 => 1,
				3 => 2,
				4 => 3,
				1 => 4,
				2 => 5,
		];
		return $faces[$data];
	}

	/**
	 * Sets the direction this button is pointing toward
	 */
	public function setFacingDirection($face){
		$data = ($this->meta ^ 0x08);
			$faces = [
				0 => 5,
				1 => 0,
				2 => 3,
				3 => 4,
				4 => 1,
				5 => 2,
			];
			$face-=1;
			if($face<0)
				$face=5;
		$this->setDamage($data |= $faces[$face]);
	}
	
	public function onRun($currentTick){
		
	}

	public function __toString(){
		return $this->getName() . " " . ($this->isPowered()?"":"NOT ") . "POWERED";
	}

}