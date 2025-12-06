<?php

class GDNDocument
{
    private $db; // Database connection

    // Properties mapping to table columns
    public $gdnId;
    public $orderManagementId;
    public $gdnNumberPrefix;
    public $gdnNumber;
    public $originWHID;
    public $destinationWHID;
    public $customerId;
    public $documentId;
    public $transactionType;
    public $carrier;
    public $carrierWaybill;
    public $shippingDate;
    public $packageValue;
    public $boxQty;
    public $gdnStatus;
    public $procurementManagerGDNApproved;
    public $salesPersonId;
    public $taxes;
    public $shippingCost;
    public $customDuties;
    public $foreignTransactionFee;
    public $insurance;
    public $surcharge;
    public $other;
    public $shippingSubTotal;
    public $createdAt;
    public $updatedAt;
    public $active;

    public function __construct()
    {
        global $db;
        $this->db = $db; // Assign the database connection instance
    }

    /**
     * Load a pick and pack document by ID
     */
    public function loadById($id)
    {
        try {
            $query = "SELECT * FROM pick_and_pack_documents WHERE gdnId = ?s";
            $row = $this->db->getRow($query, $id);

            if ($row) {
                $this->setProperties($row);
                return true;
            } else {
                throw new Exception("Pick and pack document not found.");
            }
        } catch (Exception $e) {
            error_log("Error loading pick and pack document: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Set properties from the fetched row
     */
    private function setProperties($row)
    {
        $this->gdnId = $row['gdnId'];
        $this->orderManagementId = $row['orderManagementId'];
        $this->gdnNumberPrefix = $row['gdnNumberPrefix'];
        $this->gdnNumber = $row['gdnNumber'];
        $this->originWHID = $row['originWHID'];
        $this->destinationWHID = $row['destinationWHID'];
        $this->customerId = $row['customerId'];
        $this->documentId = $row['documentId'];
        $this->transactionType = $row['transactionType'];
        $this->carrier = $row['carrier'];
        $this->carrierWaybill = $row['carrierWaybill'];
        $this->shippingDate = $row['shippingDate'];
        $this->packageValue = $row['packageValue'];
        $this->boxQty = $row['boxQty'];
        $this->gdnStatus = $row['gdnStatus'];
        $this->procurementManagerGDNApproved = $row['procurementManagerGDNApproved'];
        $this->salesPersonId = $row['salesPersonId'];
        $this->taxes = $row['taxes'];
        $this->shippingCost = $row['shippingCost'];
        $this->customDuties = $row['customDuties'];
        $this->foreignTransactionFee = $row['foreignTransactionFee'];
        $this->insurance = $row['insurance'];
        $this->surcharge = $row['surcharge'];
        $this->other = $row['other'];
        $this->shippingSubTotal = $row['shippingSubTotal'];
        $this->createdAt = $row['createdAt'];
        $this->updatedAt = $row['updatedAt'];
        $this->active = $row['active'];
    }

    /**
     * Save the document (insert or update)
     */
    public function save()
    {
        try {
            if (empty($this->gdnId)) {
                // Insert new record
                $this->db->query(
                    "INSERT INTO pick_and_pack_documents (
                        orderManagementId, gdnNumberPrefix, gdnNumber, originWHID, destinationWHID,
                        customerId, documentId, transactionType, carrier, carrierWaybill,
                        shippingDate, packageValue, boxQty, gdnStatus, procurementManagerGDNApproved,
                        salesPersonId, taxes, shippingCost, customDuties, foreignTransactionFee,
                        insurance, surcharge, other, shippingSubTotal, active
                    ) VALUES (
                        ?s, ?s, ?s, ?s, ?s,
                        ?s, ?s, ?s, ?s, ?s,
                        ?s, ?s, ?s, ?s, ?s,
                        ?s, ?s, ?s, ?s, ?s,
                        ?s, ?s, ?s, ?s, ?s
                    )",
                    $this->orderManagementId,
                    $this->gdnNumberPrefix,
                    $this->gdnNumber,
                    $this->originWHID,
                    $this->destinationWHID,
                    $this->customerId,
                    $this->documentId,
                    $this->transactionType,
                    $this->carrier,
                    $this->carrierWaybill,
                    $this->shippingDate,
                    $this->packageValue,
                    $this->boxQty,
                    $this->gdnStatus,
                    $this->procurementManagerGDNApproved,
                    $this->salesPersonId,
                    $this->taxes,
                    $this->shippingCost,
                    $this->customDuties,
                    $this->foreignTransactionFee,
                    $this->insurance,
                    $this->surcharge,
                    $this->other,
                    $this->shippingSubTotal,
                    $this->active
                );

                $this->gdnId = $this->db->insertId();
            } else {
                // Update existing record
                $this->db->query(
                    "UPDATE pick_and_pack_documents SET
                        orderManagementId = ?s,
                        gdnNumberPrefix = ?s,
                        gdnNumber = ?s,
                        originWHID = ?s,
                        destinationWHID = ?s,
                        customerId = ?s,
                        documentId = ?s,
                        transactionType = ?s,
                        carrier = ?s,
                        carrierWaybill = ?s,
                        shippingDate = ?s,
                        packageValue = ?s,
                        boxQty = ?s,
                        gdnStatus = ?s,
                        procurementManagerGDNApproved = ?s,
                        salesPersonId = ?s,
                        taxes = ?s,
                        shippingCost = ?s,
                        customDuties = ?s,
                        foreignTransactionFee = ?s,
                        insurance = ?s,
                        surcharge = ?s,
                        other = ?s,
                        shippingSubTotal = ?s,
                        active = ?s
                    WHERE gdnId = ?s",
                    $this->orderManagementId,
                    $this->gdnNumberPrefix,
                    $this->gdnNumber,
                    $this->originWHID,
                    $this->destinationWHID,
                    $this->customerId,
                    $this->documentId,
                    $this->transactionType,
                    $this->carrier,
                    $this->carrierWaybill,
                    $this->shippingDate,
                    $this->packageValue,
                    $this->boxQty,
                    $this->gdnStatus,
                    $this->procurementManagerGDNApproved,
                    $this->salesPersonId,
                    $this->taxes,
                    $this->shippingCost,
                    $this->customDuties,
                    $this->foreignTransactionFee,
                    $this->insurance,
                    $this->surcharge,
                    $this->other,
                    $this->shippingSubTotal,
                    $this->active,
                    $this->gdnId
                );
            }

            return true;
        } catch (Exception $e) {
            error_log("Error saving pick and pack document: " . $e->getMessage());
            return false;
        }
    }
}