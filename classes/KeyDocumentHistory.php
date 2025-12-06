<?php

class KeyDocumentHistory
{
    private $db; // Database connection instance
    public  $documentId;
    public  $timeline = [];

    // Constructor to initialize the database connection
    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    // Method to load document details and history by documentId
    public function loadByDocumentId($documentId)
    {
        try {

            $query = "select * from key_document_history WHERE keyDocumentId  = ?s ORDER BY updatedAt DESC";
            $res   = $this->db->query($query, $documentId);

            while ($row = $this->db->fetch($res)) {

                $this->timeline[] = new KeyDocumentHistoryItem(documentId: $row['keyDocumentId'],
                    operationType: $row['operationType'],
                    remark: $row['remark'],
                    updatedBy: $row['updatedBy'],
                    updatedAt: $row['updatedAt']);
            }

        } catch (Exception $e) {
            echo "Error loading document history: " . $e->getMessage();
            return false;
        }
        return false;
    }
}