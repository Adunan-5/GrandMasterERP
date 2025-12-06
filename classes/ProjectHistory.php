<?php

class ProjectHistory
{
    private $db; // Database connection instance
    public  $projectId;
    public  $timeline = [];

    // Constructor to initialize the database connection
    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    // Method to load project details and history by projectId
    public function loadByProjectId($projectId)
    {
        try {

            $query = "select * from consultation_project_history WHERE projectId  = ?s ORDER BY updatedAt DESC";
            $res   = $this->db->query($query, $projectId);

            while ($row = $this->db->fetch($res)) {

                $this->timeline[] = new ProjectHistoryItem(projectId: $row['projectId'],
                    operationType: $row['operationType'],
                    remark: $row['remark'],
                    updatedBy: $row['updatedBy'],
                    updatedAt: $row['updatedAt']);
            }

        } catch (Exception $e) {
            echo "Error loading project history: " . $e->getMessage();
            return false;
        }
        return false;
    }
}