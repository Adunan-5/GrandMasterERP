<?php

class ConsultationProposalDocument
{
    private $db; // Database connection
    public  $proposalID;
    public  $proposalNumberPrefix;
    public  $proposalNumber;
    public  $proposalTemplateID;
    public  $proposalTitle;
    public  $paymentTermId;
    public  $customerID;
    public  $dateCreated;
    public  $dateModified;
    public  $preparedOn;
    public  $validUntil;
    public  $contentIntroduction;
    public  $contentWhyChooseUs;
    public  $contentScopeOfWork;
    public  $contentProjectTimeLine;
    public  $contentClientResponsibilities;
    public  $contentTermsAndConditions;
    public  $contentNextSteps;
    public  $status;
    public  $approvedDate;
    public  $rejectedDate;
    public  $createdBy;
    public  $customerPassword;

    public function __construct()
    {
        global $db;
        $this->db = $db; // Assign the database connection instance
    }

    public function loadById($id)
    {
        try {
            $query = "SELECT * FROM consultation_proposals WHERE proposalID = ?s";
            $row   = $this->db->getRow($query, $id); // Assuming $db is a database abstraction layer

            if ($row) {
                $this->setProperties($row);
                return true;
            } else {
                throw new Exception("Proposal not found.");
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
        $this->proposalID                  = $row['proposalID'];
        $this->proposalNumberPrefix        = $row['proposalNumberPrefix'];
        $this->proposalNumber              = $row['proposalNumber'];
        $this->proposalTemplateID          = $row['proposalTemplateID'];
        $this->proposalTitle               = $row['proposalTitle'];
        $this->paymentTermId              = $row['paymentTermId'];
        $this->customerID                  = $row['customerID'];
        $this->dateCreated                 = $row['dateCreated'];
        $this->dateModified                = $row['dateModified'];
        $this->preparedOn                  = $row['preparedOn'];
        $this->validUntil                 = $row['validUntil'];
        $this->contentIntroduction         = $row['contentIntroduction'];
        $this->contentWhyChooseUs          = $row['contentWhyChooseUs'];
        $this->contentScopeOfWork          = $row['contentScopeOfWork'];
        $this->contentProjectTimeLine      = $row['contentProjectTimeLine'];
        $this->contentClientResponsibilities = $row['contentClientResponsibilities'];
        $this->contentTermsAndConditions   = $row['contentTermsAndConditions'];
        $this->contentNextSteps            = $row['contentNextSteps'];
        $this->status                      = $row['status'];
        $this->approvedDate                = $row['approvedDate'];
        $this->rejectedDate                = $row['rejectedDate'];
        $this->createdBy                   = $row['createdBy'];
        $this->customerPassword           = $row['customerPassword'];
    }

    /**
     * Get the proposal number with prefix
     */
    public function getProposalNumberWithPrefix()
    {
        return $this->proposalNumberPrefix . $this->proposalNumber;
    }

    /**
     * Get the template name if proposalTemplateID exists
     */
    public function getProposalTemplateName()
    {
        try {
            if (!$this->proposalTemplateID) {
                return null;
            }

            $query = "SELECT templateName FROM proposal_templates WHERE templateID = ?s";
            $template = $this->db->getRow($query, $this->proposalTemplateID);

            return $template ? $template['templateName'] : null;
        } catch (Exception $e) {
            echo "Error fetching proposal template name: " . $e->getMessage();
            return null;
        }
    }
}