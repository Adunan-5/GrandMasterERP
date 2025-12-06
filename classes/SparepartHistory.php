<?php

class SparepartHistory
{
    private $db; // Database connection instance
    public  $sparepartId;
    public  $timeline = [];

    // Constructor to initialize the database connection
    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    // Method to load sparepart details and history by sparepartId
    public function loadBySparepartId($sparepartId)
    {
        try {

            $query = "select * from spareparts_history WHERE sparepartId  = ?s ORDER BY updatedAt DESC";
            $res   = $this->db->query($query, $sparepartId);

            while ($row = $this->db->fetch($res)) {

                $this->timeline[] = new SparepartHistoryItem(sparepartId: $row['sparepartId'],
                    operationType: $row['operationType'],
                    remark: $row['remark'],
                    updatedBy: $row['updatedBy'],
                    updatedAt: $row['updatedAt']);
            }

        } catch (Exception $e) {
            echo "Error loading sparepart history: " . $e->getMessage();
            return false;
        }
        return false;
    }
}