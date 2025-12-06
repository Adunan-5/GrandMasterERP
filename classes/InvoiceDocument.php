<?php

class InvoiceDocument
{
    private $db; // Database connection
    public  $invoiceId;
    public  $createdBy;
    public  $createdAt;
    public  $updatedAt;
    public  $customerId;
    public  $invoiceNumberPrefix;
    public  $invoiceNumber;
    public  $invoiceDateIssued;
    public  $invoiceStatus;
    public  $saleOrderId;
    public  $accountantInvoiceApproved;
    public  $salesPersonId;
    public  $isFullyInvoiced;
    public  $PONumber;
    public  $POAttachment;
    public  $paymentTermId;
    public  $companyId;
    public  $totalAmount;

    public function __construct()
    {
        global $db;
        $this->db = $db; // Assign the database connection instance
    }

    public function loadById($id)
    {
        try {
            $query = "SELECT * FROM invoice_documents WHERE invoiceId = ?s";
            $row   = $this->db->getRow($query, $id); // Assuming $db is a database abstraction layer

            if ($row) {
                $this->setProperties($row);
                return true;
            } else {
                throw new Exception("Invoice document not found.");
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
        $this->invoiceId                   = $row['invoiceId'];
        $this->createdBy                   = $row['createdBy'];
        $this->createdAt                   = $row['createdAt'];
        $this->updatedAt                   = $row['updatedAt'];
        $this->customerId                  = $row['customerId'];
        $this->invoiceNumberPrefix        = $row['invoiceNumberPrefix'];
        $this->invoiceNumber              = $row['invoiceNumber'];
        $this->invoiceDateIssued          = $row['invoiceDateIssued'];
        $this->invoiceStatus               = $row['invoiceStatus'];
        $this->saleOrderId                 = $row['saleOrderId'];
        $this->accountantInvoiceApproved   = $row['accountantInvoiceApproved'];
        $this->salesPersonId               = $row['salesPersonId'];
        $this->isFullyInvoiced             = $row['isFullyInvoiced'];
        $this->PONumber                   = $row['PONumber'];
        $this->POAttachment               = $row['POAttachment'];
        $this->paymentTermId              = $row['paymentTermId'];
        $this->companyId              = $row['companyId'];
        $this->totalAmount                 = $row['totalAmount'];
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
    
}