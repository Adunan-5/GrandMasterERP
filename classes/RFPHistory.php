<?php

class RFPHistory
{
    private $db; // Database connection instance
    public  $rfpId;
    public  $timeline = [];

    // Constructor to initialize the database connection
    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    // Method to load rfp details and history by rfpId
    public function loadByRFPId($rfpId)
    {
        try {

            $query = "select * from rfp_document_history WHERE rfpId  = ?s ORDER BY updatedAt DESC";
            $res   = $this->db->query($query, $rfpId);

            while ($row = $this->db->fetch($res)) {

                $this->timeline[] = new RFPHistoryItem(rfpId: $row['rfpId'],
                    operationType: $row['operationType'],
                    remark: $row['remark'],
                    updatedBy: $row['updatedBy'],
                    updatedAt: $row['updatedAt']);
            }

        } catch (Exception $e) {
            echo "Error loading RFP history: " . $e->getMessage();
            return false;
        }
        return false;
    }
}