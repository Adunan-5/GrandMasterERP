<?php

class ConsultationPOHistory
{
    private $db; // Database connection instance
    public  $poId;
    public  $timeline = [];

    // Constructor to initialize the database connection
    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    // Method to load po details and history by poId
    public function loadByPOId($poId)
    {
        try {

            $query = "select * from consultation_po_document_history WHERE poId  = ?s ORDER BY updatedAt DESC";
            $res   = $this->db->query($query, $poId);

            while ($row = $this->db->fetch($res)) {

                $this->timeline[] = new ConsultationPOHistoryItem(poId: $row['poId'],
                    operationType: $row['operationType'],
                    remark: $row['remark'],
                    updatedBy: $row['updatedBy'],
                    updatedAt: $row['updatedAt']);
            }

        } catch (Exception $e) {
            echo "Error loading PO history: " . $e->getMessage();
            return false;
        }
        return false;
    }
}