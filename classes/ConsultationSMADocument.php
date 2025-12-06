<?php

class ConsultationSMADocument
{
    private $db; // Database connection
    public  $smaID;
    public  $customerID;
    public  $associatedProposalID;
    public  $createdBy;
    public  $dateCreated;
    public  $dateModified;
    public  $validDays;
    public  $content;
    public  $active;
    public  $status;
    public  $smaNumberPrefix;
    public  $smaNumber;
    public  $totalAmount;
    public  $discountType;
    public  $smaDiscount;
    public  $smaTemplateID;

    public function __construct()
    {
        global $db;
        $this->db = $db; // Assign the database connection instance
    }

    public function loadById($id)
    {
        try {
            $query = "SELECT * FROM consultation_sma WHERE smaID = ?s";
            $row   = $this->db->getRow($query, $id); // Assuming $db is a database abstraction layer

            if ($row) {
                $this->setProperties($row);
                return true;
            } else {
                throw new Exception("SMA document not found.");
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
        $this->smaID               = $row['smaID'];
        $this->customerID          = $row['customerID'];
        $this->associatedProposalID= $row['associatedProposalID'];
        $this->createdBy           = $row['createdBy'];
        $this->dateCreated         = $row['dateCreated'];
        $this->dateModified        = $row['dateModified'];
        $this->validDays          = $row['validDays'];
        $this->content            = $row['content'];
        $this->active             = $row['active'];
        $this->status             = $row['status'];
        $this->smaNumberPrefix    = $row['smaNumberPrefix'];
        $this->smaNumber          = $row['smaNumber'];
        $this->totalAmount        = $row['totalAmount'];
        $this->discountType        = $row['discountType'];
        $this->smaDiscount        = $row['smaDiscount'];
        $this->smaTemplateID      = $row['smaTemplateID'];
    }

    /**
     * Get the SMA number with prefix
     */
    public function getSMANumberWithPrefix()
    {
        return $this->smaNumberPrefix . $this->smaNumber;
    }

    /**
     * Get the template name if smaTemplateID exists
     */
    public function getSMATemplateName()
    {
        try {
            if (!$this->smaTemplateID) {
                return null;
            }

            $query = "SELECT templateName FROM sma_templates WHERE templateID = ?s";
            $template = $this->db->getRow($query, $this->smaTemplateID);

            return $template ? $template['templateName'] : null;
        } catch (Exception $e) {
            echo "Error fetching SMA template name: " . $e->getMessage();
            return null;
        }
    }
}