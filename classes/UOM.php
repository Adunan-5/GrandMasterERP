<?php
class UOM {
    public $uomId;
    public $uomName;
    public $uomSymbol;
    public $categoryId;
    public $type;
    public $ratio;
    public $roundingPrecision;
    public $active;

    public static function getUOMByCategory($categoryId) {
        global $db;
        $query = "SELECT * FROM `uoms` WHERE `categoryId` = ?i";
        $result = $db->getAll($query, $categoryId);
        return array_map(fn($row) => (object) $row, $result);
    }

    public static function calculateQuantity($quantity, $uomId) {
        global $db;

        // Fetch the type and ratio for the given uomId
        $query = "SELECT `type`, `ratio` FROM `uoms` WHERE `uomId` = ?i";
        $uom = $db->getRow($query, $uomId);

        if (!$uom) {
            throw new Exception("Invalid UOM ID: $uomId");
        }

        $type = $uom['type'];
        $ratio = $uom['ratio'];

        // Perform the calculation based on the type
        if ($type === 'Bigger') {
            return $quantity / $ratio;
        } elseif ($type === 'Smaller') {
            return $quantity * $ratio;
        }

        // For Reference type, return the original quantity
        return $quantity;
    }

}