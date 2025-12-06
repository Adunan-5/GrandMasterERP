<?php

class RFPDocument
{
    private $db; // Database connection
    public  $rfpId;
    public  $createdBy;
    public  $createdAt;
    public  $updatedAt;
    public  $rfpNumberPrefix;
    public  $rfpNumber;
    public  $saleOrderId;
    public  $supplierId;
    public  $warehouseId;
    public  $rfpDateCreated;
    public  $etaDate;
    public  $supplierQuotationNumber;
    public  $supplierQuotationAttachment;
    public  $rfpStatus;
    public  $notes;
    public  $rfpReason;
    public  $procurementManagerRFPApproved;
    public  $accountantRFPApproved;
    public  $salesPersonId;
    public  $paymentTermId;
    public  $totalAmount;
    public function __construct()
    {
        global $db;
        $this->db = $db; // Assign the database connection instance
    }


    public function loadById($id)
    {
        try {
            $query = "SELECT * FROM rfp_documents WHERE rfpId = ?s";
            $row   = $this->db->getRow($query, $id); // Assuming $db is a database abstraction layer

            if ($row) {
                $this->setProperties($row);
                return true;
            } else {
                throw new Exception("Document not found.");
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
        $this->rfpId                     = $row['rfpId'];
        $this->createdBy                       = $row['createdBy'];
        $this->createdAt                       = $row['createdAt'];
        $this->updatedAt                       = $row['updatedAt'];
        $this->rfpNumberPrefix           = $row['rfpNumberPrefix'];
        $this->rfpNumber                 = $row['rfpNumber'];
        $this->saleOrderId           = $row['saleOrderId'];
        $this->supplierId                      = $row['supplierId'];
        $this->warehouseId                      = $row['warehouseId'];
        $this->rfpDateCreated             = $row['rfpDateCreated'];
        $this->etaDate             = $row['etaDate'];
        $this->supplierQuotationNumber             = $row['supplierQuotationNumber'];
        $this->supplierQuotationAttachment             = $row['supplierQuotationAttachment'];
        $this->rfpStatus     = $row['rfpStatus'];
        $this->notes = $row['notes'];
        $this->salesPersonId                   = $row['salesPersonId'];
        $this->rfpReason                 = $row['rfpReason'];
        $this->procurementManagerRFPApproved                 = $row['procurementManagerRFPApproved'];
        $this->accountantRFPApproved                        = $row['accountantRFPApproved'];
        $this->paymentTermId                   = $row['paymentTermId'];
        $this->totalAmount                     = $row['totalAmount'];
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
            $term  = $this->db->getRow($query, $this->paymentTermId);

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
            $term  = $this->db->getRow($query, $this->paymentTermId);

            return $term ? $term['termValue'] : null;
        } catch (Exception $e) {
            echo "Error fetching payment term value: " . $e->getMessage();
            return null;
        }
    }

}