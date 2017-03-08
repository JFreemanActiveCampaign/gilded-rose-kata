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
		//create two identical arrays of items
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

		//num days to text
		$maxDays = 100;

		//create a copy of the old program and the new program
		$old_prog = new Program($items_old);
		$prog = new RefactoredProgram($items_new);
		for($i = 0; $i < $maxDays; $i++) {
			$old_prog->UpdateQuality();
			$prog->UpdateQuality();

			//create a lookup so we can compare the same items
			$lookup = array();
			foreach($old_prog->getItems() as $old_item) {
				$lookup[$old_item->name] = $old_item;
			}

			//compare each item
			foreach($prog->getItems() as $item) {
				$this->assertEquals($lookup[$item->name]->quality, $item->quality, 'Day ' . $i . ' Comparing ' . $item->name . ' quality');
				$this->assertEquals($lookup[$item->name]->sellIn, $item->sellIn, 'Day ' . $i . ' Comparing ' . $item->name . ' sellIn');
			}
		}
	}
}
