<?php

class ConsultationKeyDocument
{
    private $db; // Database connection
    public  $documentId;
    public  $createdBy;
    public  $createdAt;
    public  $updatedAt;
    public  $quotationNumberPrefix;
    public  $quotationNumber;
    public  $saleOrderNumberPrefix;
    public  $proformaInvoiceNumberPrefix;
    public  $saleOrderNumber;
    public  $proformaInvoiceNumber;
    public  $customerId;
    public  $quotationDateIssued;
    public  $quotationDateExpiry;
    public  $saleOrderDateIssued;
    public  $proformaInvoiceDateIssued;
    public  $quotationAcceptedByCustomer;
    public  $quotationAcceptedByCustomerDate;
    public  $salesPersonId;
    public  $quotationStatus;
    public  $saleOrderStatus;
    public  $PONumber;
    public  $POAttachment;
    public  $paymentTermId;
    public  $totalAmount;
    public  $customerPassword;
    public  $salesManagerSOApproved;
    public  $accountantSOApproved;
    public  $associatedProposalID;

    public function __construct()
    {
        global $db;
        $this->db = $db; // Assign the database connection instance
    }


    public function loadById($id)
    {
        try {
            $query = "SELECT * FROM consultation_key_documents WHERE documentId = ?s";
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
        $this->documentId                      = $row['documentId'];
        $this->createdBy                       = $row['createdBy'];
        $this->createdAt                       = $row['createdAt'];
        $this->updatedAt                       = $row['updatedAt'];
        $this->quotationNumberPrefix           = $row['quotationNumberPrefix'];
        $this->quotationNumber                 = $row['quotationNumber'];
        $this->proformaInvoiceNumberPrefix     = $row['proformaInvoiceNumberPrefix'];
        $this->proformaInvoiceNumber           = $row['proformaInvoiceNumber'];
        $this->saleOrderNumberPrefix           = $row['saleOrderNumberPrefix'];
        $this->saleOrderNumber                 = $row['saleOrderNumber'];
        $this->customerId                      = $row['customerId'];
        $this->quotationDateIssued             = $row['quotationDateIssued'];
        $this->quotationDateExpiry             = $row['quotationDateExpiry'];
        $this->saleOrderDateIssued             = $row['saleOrderDateIssued'];
        $this->proformaInvoiceDateIssued       = $row['proformaInvoiceDateIssued'];
        $this->quotationAcceptedByCustomer     = $row['quotationAcceptedByCustomer'];
        $this->quotationAcceptedByCustomerDate = $row['quotationAcceptedByCustomerDate'];
        $this->salesPersonId                   = $row['salesPersonId'];
        $this->quotationStatus                 = $row['quotationStatus'];
        $this->saleOrderStatus                 = $row['saleOrderStatus'];
        $this->PONumber                        = $row['PONumber'];
        $this->POAttachment                    = $row['POAttachment'];
        $this->paymentTermId                   = $row['paymentTermId'];
        $this->totalAmount                     = $row['totalAmount'];
        $this->customerPassword                = $row['customerPassword'];
        $this->salesManagerSOApproved          = $row['salesManagerSOApproved'];
        $this->accountantSOApproved            = $row['accountantSOApproved'];
        $this->associatedProposalID            = $row['associatedProposalID'];
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

    public function getSalesOrderNumberWithPrefix(){
        return $this->saleOrderNumberPrefix . $this->saleOrderNumber;
    }
    public function getQuotationOrderNumberWithPrefix(){
        return $this->quotationNumberPrefix . $this->quotationNumber;
    }
}