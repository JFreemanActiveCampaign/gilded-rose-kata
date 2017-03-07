<?php

namespace GildedRose\Tests;

use PHPUnit\Framework\TestCase;
use GildedRose\RefactoredProgram;
use GildedRose\Program;
use GildedRose\Item;

class GildedRoseTest extends TestCase
{
	public function testQuality()
	{
		$items_old = array(
			new Item(array( 'name' => "+5 Dexterity Vest",'sellIn' => 10,'quality' => 20)),
			new Item(array( 'name' => "Aged Brie",'sellIn' => 2,'quality' => 0)),
			new Item(array( 'name' => "Elixir of the Mongoose",'sellIn' => 5,'quality' => 7)),
			new Item(array( 'name' => "Sulfuras, Hand of Ragnaros",'sellIn' => 0,'quality' => 80)),
			new Item(array(
				'name' => "Backstage passes to a TAFKAL80ETC concert",
				'sellIn' => 15,
				'quality' => 20
			)),
			new Item(array('name' => "Conjured Mana Cake",'sellIn' => 3,'quality' => 6)),
		);

		$items_new = array(
			new Item(array( 'name' => "+5 Dexterity Vest",'sellIn' => 10,'quality' => 20)),
			new Item(array( 'name' => "Aged Brie",'sellIn' => 2,'quality' => 0)),
			new Item(array( 'name' => "Elixir of the Mongoose",'sellIn' => 5,'quality' => 7)),
			new Item(array( 'name' => "Sulfuras, Hand of Ragnaros",'sellIn' => 0,'quality' => 80)),
			new Item(array(
				'name' => "Backstage passes to a TAFKAL80ETC concert",
				'sellIn' => 15,
				'quality' => 20
			)),
			new Item(array('name' => "Conjured Mana Cake",'sellIn' => 3,'quality' => 6)),
		);

		$maxSellIn = 0;
		foreach($items_old as $item) {
			$maxSellIn = max($maxSellIn, $item->sellIn);
		}

		$old_prog = new Program($items_old);
		$prog = new RefactoredProgram($items_new);
		for($i = 0; $i < $maxSellIn + 5; $i++) {
			$old_prog->UpdateQuality();
			$prog->UpdateQuality();

			$lookup = array();
			foreach($old_prog->getItems() as $old_item) {
				$lookup[$old_item->name] = $old_item;
			}
			foreach($prog->getItems() as $item) {
				$this->assertEquals($lookup[$item->name]->quality, $item->quality, 'Comparing ' . $item->name . ' quality');
				$this->assertEquals($lookup[$item->name]->sellIn, $item->sellIn, 'Comparing ' . $item->name . ' sellIn');
			}
		}
	}
}
