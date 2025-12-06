<?php

class PaymentTerm
{
    // Class properties
    public $termId;
    public $termName;
    public $termValue;
    public $active;

    // Constructor to initialize the properties
    public function __construct($termId = null, $termName = null, $termValue = null, $active = null)
    {
        $this->termId = $termId;
        $this->termName = $termName;
        $this->termValue = $termValue;
        $this->active = $active;
    }

    // Static function to fetch term name and value by ID
    // Static function to fetch term object by ID
    public static function getPaymentTermById($termId)
    {
        global $db; // Use the global database instance

        $result = $db->getRow("SELECT termId, termName, termValue, active FROM payment_terms WHERE termId = ?i AND active = 1", $termId);

        if ($result) {
            // Return a new PaymentTerm object
            return new PaymentTerm(
                $result['termId'],
                $result['termName'],
                $result['termValue'],
                $result['active']
            );
        } else {
            return null; // Return null if no matching term is found
        }
    }
}

?>