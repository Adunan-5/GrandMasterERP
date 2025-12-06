<?php
class UOMCategory {
    public $categoryId;
    public $categoryName;
    public $description;

    public static function getAllCategories() {
        global $db;
        $query = "SELECT * FROM `uom_categories`";
        $result = $db->getAll($query);
        return array_map(fn($row) => (object) $row, $result);
    }
}