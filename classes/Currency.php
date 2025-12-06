<?php

class Currency
{
    // Class properties
    public $currencyId;
    public $currency;
    public $currencyName;
    public $currencySymbol;
    public $currencyUnit;
    public $currencySubunit;
    public $active;
    public $dateCreated;
    public $dateUpdated;

    // Constructor to initialize the properties
    public function __construct(
        $currencyId = null,
        $currency = null,
        $currencyName = null,
        $currencySymbol = null,
        $currencyUnit = null,
        $currencySubunit = null,
        $active = null,
        $dateCreated = null,
        $dateUpdated = null
    ) {
        $this->currencyId = $currencyId;
        $this->currency = $currency;
        $this->currencyName = $currencyName;
        $this->currencySymbol = $currencySymbol;
        $this->currencyUnit = $currencyUnit;
        $this->currencySubunit = $currencySubunit;
        $this->active = $active;
        $this->dateCreated = $dateCreated;
        $this->dateUpdated = $dateUpdated;
    }

    // Static function to fetch currency object by ID
    public static function getCurrencyById($currencyId)
    {
        global $db; // Use the global database instance

        $result = $db->getRow(
            "SELECT currencyId, currency, currencyName, currencySymbol, currencyUnit, currencySubunit, active, dateCreated, dateUpdated 
            FROM currencies 
            WHERE currencyId = ?i AND active = 1",
            $currencyId
        );

        if ($result) {
            // Create a new Currency object
            $currency = new self();
            foreach ($result as $key => $value) {
                $currency->$key = $value;
            }
            return $currency;
        } else {
            return null; // Return null if no matching currency is found
        }
    }
}

?>