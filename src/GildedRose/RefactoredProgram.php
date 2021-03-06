<?php

namespace GildedRose;

/**
 * Hi and welcome to team Gilded Rose.
 *
 * As you know, we are a small inn with a prime location in a prominent city
 * ran by a friendly innkeeper named Allison. We also buy and sell only the
 * finest goods. Unfortunately, our goods are constantly degrading in quality
 * as they approach their sell by date. We have a system in place that updates
 * our inventory for us. It was developed by a no-nonsense type named Leeroy,
 * who has moved on to new adventures. Your task is to add the new feature to
 * our system so that we can begin selling a new category of items. First an
 * introduction to our system:
 *
 * - All items have a SellIn value which denotes the number of days we have to sell the item
 * - All items have a Quality value which denotes how valuable the item is
 * - At the end of each day our system lowers both values for every item
 *
 * Pretty simple, right? Well this is where it gets interesting:
 *
 * - Once the sell by date has passed, Quality degrades twice as fast
 * - The Quality of an item is never negative
 * - "Aged Brie" actually increases in Quality the older it gets
 * - The Quality of an item is never more than 50
 * - "Sulfuras", being a legendary item, never has to be sold or decreases in Quality
 * - "Backstage passes", like aged brie, increases in Quality as it's SellIn
 *   value approaches; Quality increases by 2 when there are 10 days or less and
 *   by 3 when there are 5 days or less but Quality drops to 0 after the concert
 *
 * We have recently signed a supplier of conjured items. This requires an
 * update to our system:
 *
 * - "Conjured" items degrade in Quality twice as fast as normal items
 *
 * Feel free to make any changes to the UpdateQuality method and add any new
 * code as long as everything still works correctly. However, do not alter the
 * Item class or Items property as those belong to the goblin in the corner who
 * will insta-rage and one-shot you as he doesn't believe in shared code
 * ownership (you can make the UpdateQuality method and Items property static
 * if you like, we'll cover for you).
 *
 * Just for clarification, an item can never have its Quality increase above
 * 50, however "Sulfuras" is a legendary item and as such its Quality is 80 and
 * it never alters.
 */
class RefactoredProgram extends \GildedRose\Program
{
    private $items = array();

    public static function Main($days = 1)
    {
        echo "OMGHAI!\n";

        $app = new RefactoredProgram(array(
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
        ));

        for ($i = 1; $i <= $days; $i++) {
            $app->UpdateQuality();
            echo "-------- day $i --------\n";
            echo sprintf("%50s - %7s - %7s\n", "Name", "SellIn", "Quality");
            foreach ($app->items as $item) {
                echo sprintf("%50s - %7d - %7d\n", $item->name, $item->sellIn, $item->quality);
            }
        }
    }

    public function __construct(array $items)
    {
        $this->items = $items;
    }

    public function getItems() {
    	return $this->items;
	}

	public function UpdateQuality()
	{
		$quality_increases_whitelist = array(
			'Aged Brie'=>true,
			'Backstage passes to a TAFKAL80ETC concert'=>true,
		);
		$quality_static_whitelist = array(
			'Sulfuras, Hand of Ragnaros' => true,
		);
		$quality_expires_whitelist = array(
			'Backstage passes to a TAFKAL80ETC concert' => true,
		);
		$quality_increases_after_expire_whitelist = array(
			'Aged Brie'=>true,
		);
		foreach ($this->items as $item) {
			$original_quality = $item->quality;
			$original_sellIn = $item->sellIn;

			//quality increases each day
			$quality_increases = isset($quality_increases_whitelist[$item->name]);
			//quality increases after expiry
			$quality_increases_after_expire = isset($quality_increases_after_expire_whitelist[$item->name]);
			//quality does not change
			$quality_static = isset($quality_static_whitelist[$item->name]);
			//quality decreases each day
			$quality_decreases = !$quality_increases && !$quality_static;
			//quality is 0 after expiry
			$expires_instantly = isset($quality_expires_whitelist[$item->name]);

			//whether the item is conjured
			$is_conjured = stripos($item->name, 'conjured') !== false;


			//max quality is 50, unless the quality is already over 50
			$max_quality = max(50, $item->quality);

			if($quality_increases) {
				$item->quality = $item->quality + 1;

				//backstage passes get more valuable close to expiry
				if ($item->name == "Backstage passes to a TAFKAL80ETC concert") {
					if ($item->sellIn < 11) {
						$item->quality = $item->quality + 1;
					}

					if ($item->sellIn < 6) {
						$item->quality = $item->quality + 1;
					}
				}
			} else {
				$item->quality = $item->quality - 1;
			}

			//one day has passed
			$item->sellIn = $item->sellIn - 1;

			$item_expired = $item->sellIn < 0;

			//item is past its sell by date
			if ($item_expired) {

				//quality decreases each day
				if($quality_decreases) {
					$item->quality = $item->quality - 1;
				}
				//quality increases after expiry
				if($quality_increases_after_expire) {
					$item->quality = $item->quality + 1;
				}
				//item has fully expired

				if($expires_instantly) {
					$item->quality = 0;
				}
			}

			//conjured items should degrade twice as fast
			if($is_conjured) {
				$quality_diff = $original_quality - $item->quality;
				if($quality_diff > 0) {
					$item->quality = $original_quality - $quality_diff * 2;
				}
			}

			//static items do not degrade
			if($quality_static) {
				$item->quality = $original_quality;
				$item->sellIn = $original_sellIn;
			}

			//item quality should never be less than 0
			$item->quality = max(0, $item->quality);

			//item quality should not exceed maximum
			$item->quality = min($max_quality, $item->quality);
		}
	}
}
