<?php

class OrderManagement
{
    private $db; // Database connection
    public $orderManagementId;
    public $orderNumber;
    public $createdBy;
    public $orderWHStatus;
    public $originWHId;
    public $destinationWHId;
    public $customerAccount;
    public $transactionType;
    public $shipmentNumberPrefix;
    public $shipmentNumber;
    public $salesPersonId;
    public $customerId;
    public $documentId;
    public $shipmentId;
    public $notes;
    public $active;
    public $createdAt;
    public $updatedAt;

    public function __construct()
    {
        global $db;
        $this->db = $db; // Assign the database connection instance
    }

    public function loadById($id)
    {
        try {
            $query = "SELECT * FROM order_management_documents WHERE orderManagementId = ?s";
            $row = $this->db->getRow($query, $id); // Assuming $db is a database abstraction layer

            if ($row) {
                $this->setProperties($row);
                return true;
            } else {
                throw new Exception("Order management document not found.");
            }
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            return false;
        }
    }

    /**
     * Set properties from the fetched row
     */
    private function setProperties($row)
    {
        $this->orderManagementId = $row['orderManagementId'];
        $this->orderNumber = $row['orderNumber'];
        $this->createdBy = $row['createdBy'];
        $this->orderWHStatus = $row['orderWHStatus'];
        $this->originWHId = $row['originWHId'];
        $this->destinationWHId = $row['destinationWHId'];
        $this->customerAccount = $row['customerAccount'];
        $this->transactionType = $row['transactionType'];
        $this->shipmentNumberPrefix = $row['shipmentNumberPrefix'];
        $this->shipmentNumber = $row['shipmentNumber'];
        $this->salesPersonId = $row['salesPersonId'];
        $this->customerId = $row['customerId'];
        $this->documentId = $row['documentId'];
        $this->shipmentId = $row['shipmentId'];
        $this->notes = $row['notes'];
        $this->active = $row['active'];
        $this->createdAt = $row['createdAt'];
        $this->updatedAt = $row['updatedAt'];
    }


}