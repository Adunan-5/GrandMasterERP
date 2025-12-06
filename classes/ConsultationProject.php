<?php

class ConsultationProject
{
    private $db; // Database connection
    public $projectId;
    public $documentId;
    public $projectNumberPrefix;
    public $projectNumber;
    public $projectHeadId;
    public $quotationStatus;
    public $createdBy;
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
            $query = "SELECT * FROM consultation_projects WHERE projectId = ?s";
            $row = $this->db->getRow($query, $id);

            if ($row) {
                $this->setProperties($row);
                return true;
            } else {
                throw new Exception("Project not found.");
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
        $this->projectId = $row['projectId'];
        $this->documentId = $row['documentId'];
        $this->projectNumberPrefix = $row['projectNumberPrefix'];
        $this->projectNumber = $row['projectNumber'];
        $this->projectHeadId = $row['projectHeadId'];
        $this->quotationStatus = $row['quotationStatus'];
        $this->createdBy = $row['createdBy'];
        $this->createdAt = $row['createdAt'];
        $this->updatedAt = $row['updatedAt'];
    }

}