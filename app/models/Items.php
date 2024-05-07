<?php

use Phalcon\Mvc\Model;

class Items extends Model
{
    public $id;
    public $item_name;
    public $item_url;
    public $details;
    public $price;

   

    // Retrieve all items
    public static function getAllItems()
    {
        return self::find();
    }

    // Add a new item
    public static function addItem($itemData)
    {
        $item = new self();
        $item->item_name = $itemData['item_name'];
        $item->item_url = $itemData['item_index'];
        $item->details = $itemData['details'];
        $item->price = $itemData['price'];

        if ($item->save()) {
            return true;
        } else {
            return false;
        }
    }
}
?>
