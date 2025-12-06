<?php

class PurchaseOrder
{
    private $db; // Database connection
    public $poId;
    public $rfpId;
    public $poDateCreated;
    public $poNumberPrefix;
    public $poNumber;
    public $supplierId;
    public $salesPersonId;
    public $paymentTermId;
    public $createdBy;
    public $createdAt;
    public $poStatus;
    public $updatedAt;
    public $totalAmount;
    public $notes;
    public $shippingMethod;
    public $shippingCost;
    public $trackingNumber;
    public $rfpReason;
    public $estimatedArrival;
    public $warehouseId;
    public $supplierQuotationNumber;
    public $supplierQuotationAttachment;
    public $paymentRefNo;
    public $paymentRefAttachment;
    public $supplierInvoiceAttachment;
    public $supplierInvoiceNo;
    public $supplierInvoiceDate;
    public $vatAmount;
    public $paymentDate;
    public $subTotal;

    public $spManagerPOApproved;
    public $accountantPOApproved;
    public $etaDate;
    public $active;

    public function __construct()
    {
        global $db;
        $this->db = $db; // Assign the database connection instance
    }

    public function loadById($id)
    {
        try {
            $query = "SELECT * FROM purchase_orders WHERE poId = ?s";
            $row = $this->db->getRow($query, $id); // Assuming $db is a database abstraction layer

            if ($row) {
                $this->setProperties($row);
                return true;
            } else {
                throw new Exception("Purchase Order not found.");
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
        $this->poId = $row['poId'];
        $this->rfpId = $row['rfpId'];
        $this->poDateCreated = $row['poDateCreated'];
        $this->poNumberPrefix = $row['poNumberPrefix'];
        $this->poNumber = $row['poNumber'];
        $this->supplierId = $row['supplierId'];
        $this->salesPersonId = $row['salesPersonId'];
        $this->paymentTermId = $row['paymentTermId'];
        $this->createdBy = $row['createdBy'];
        $this->createdAt = $row['createdAt'];
        $this->poStatus = $row['poStatus'];
        $this->updatedAt = $row['updatedAt'];
        $this->totalAmount = $row['totalAmount'];
        $this->notes = $row['notes'];
        $this->shippingMethod = $row['shippingMethod'];
        $this->shippingCost = $row['shippingCost'];
        $this->trackingNumber = $row['trackingNumber'];
        $this->paymentRefAttachment = $row['paymentRefAttachment'];
        $this->paymentRefNo = $row['paymentRefNo'];
        $this->paymentDate = $row['paymentDate'];
        $this->subTotal = $row['subTotal'];
        $this->rfpReason = $row['rfpReason'];
        $this->estimatedArrival = $row['estimatedArrival'];
        $this->warehouseId = $row['warehouseId'];
        $this->supplierQuotationNumber = $row['supplierQuotationNumber'];
        $this->supplierQuotationAttachment = $row['supplierQuotationAttachment'];
        $this->supplierInvoiceAttachment = $row['supplierInvoiceAttachment'];
        $this->supplierInvoiceNo = $row['supplierInvoiceNo'];
        $this->supplierInvoiceDate = $row['supplierInvoiceDate'];
        $this->vatAmount = $row['vatAmount'];
        $this->spManagerPOApproved = $row['spManagerPOApproved'];
        $this->accountantPOApproved = $row['accountantPOApproved'];
        $this->etaDate = $row['etaDate'];
        $this->active = $row['active'];
    }

    /**
     * Get the name of the payment term
     */
    public function getPaymentTermName()
    {
        try {
            if (!$this->paymentTermId) {
                return null;
            }

            $query = "SELECT termName FROM payment_terms WHERE termId = ?s";
            $term = $this->db->getRow($query, $this->paymentTermId);

            return $term ? $term['termName'] : null;
        } catch (Exception $e) {
            echo "Error fetching payment term name: " . $e->getMessage();
            return null;
        }
    }

    /**
     * Get the value of the payment term
     */
    public function getPaymentTermValue()
    {
        try {
            if (!$this->paymentTermId) {
                return null;
            }

            $query = "SELECT termValue FROM payment_terms WHERE termId = ?s";
            $term = $this->db->getRow($query, $this->paymentTermId);

            return $term ? $term['termValue'] : null;
        } catch (Exception $e) {
            echo "Error fetching payment term value: " . $e->getMessage();
            return null;
        }
    }

}