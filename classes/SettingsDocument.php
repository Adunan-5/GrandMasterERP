<?php

class SettingsDocument
{
    private $db; // SafeMySQL instance
    public $documentSettingId;
    public $titleFontSize;
    public $titleFontSize_ar;
    public $titleFontFamilyId;
    public $titleFontFamilyId_ar;
    public $headerPrimaryFontSize;
    public $headerPrimaryFontSize_ar;
    public $headerPrimaryFontFamilyId;
    public $headerPrimaryFontFamilyId_ar;
    public $headerSecondaryFontSize;
    public $headerSecondaryFontSize_ar;
    public $headerSecondaryFontFamilyId;
    public $headerSecondaryFontFamilyId_ar;
    public $bodyFontSize;
    public $bodyFontSize_ar;
    public $bodyFontFamilyId;
    public $bodyFontFamilyId_ar;
    public $tableHeaderFontSize;
    public $tableHeaderFontSize_ar;
    public $tableHeaderFontFamilyId;
    public $tableHeaderFontFamilyId_ar;
    public $tableRowsFontSize;
    public $tableRowsFontSize_ar;
    public $tableRowsFontFamilyId;
    public $tableRowsFontFamilyId_ar;
    public $footerFontSize;
    public $footerFontSize_ar;
    public $footerFontFamilyId;
    public $footerFontFamilyId_ar;
    public $createdAt;
    public $updatedAt;

    // Constructor to initialize database instance
    public function __construct($db)
    {
        $this->db = $db;
    }

    // Load a settings document by ID
    public function loadById($id)
    {
        try {
            $query = "SELECT * FROM settings_document WHERE documentSettingId = ?s";
            $row = $this->db->getRow($query, $id);

            if ($row) {
                $this->setProperties($row);
                return true;
            } else {
                throw new Exception("Settings document not found.");
            }
        } catch (Exception $e) {
            echo "Failed to load settings document: " . $e->getMessage();
            return false;
        }
    }

    // Set properties from an array
    private function setProperties($row)
    {
        foreach ($row as $key => $value) {
            $this->$key = $value;
        }
    }

    // Save updates to the database
    public function save()
    {
        try {
            $query = "UPDATE settings_document SET 
                titleFontSize = ?s,
                titleFontSize_ar = ?s,
                titleFontFamilyId = ?s,
                titleFontFamilyId_ar = ?s,
                headerPrimaryFontSize = ?s,
                headerPrimaryFontSize_ar = ?s,
                headerPrimaryFontFamilyId = ?s,
                headerPrimaryFontFamilyId_ar = ?s,
                headerSecondaryFontSize = ?s,
                headerSecondaryFontSize_ar = ?s,
                headerSecondaryFontFamilyId = ?s,
                headerSecondaryFontFamilyId_ar = ?s,
                bodyFontSize = ?s,
                bodyFontSize_ar = ?s,
                bodyFontFamilyId = ?s,
                bodyFontFamilyId_ar = ?s,
                tableHeaderFontSize = ?s,
                tableHeaderFontSize_ar = ?s,
                tableHeaderFontFamilyId = ?s,
                tableHeaderFontFamilyId_ar = ?s,
                tableRowsFontSize = ?s,
                tableRowsFontSize_ar = ?s,
                tableRowsFontFamilyId = ?s,
                tableRowsFontFamilyId_ar = ?s,
                footerFontSize = ?s,
                footerFontSize_ar = ?s,
                footerFontFamilyId = ?s,
                footerFontFamilyId_ar = ?s,
                updatedAt = NOW()
                WHERE documentSettingId = ?s";

            $this->db->query($query,
                $this->titleFontSize, $this->titleFontSize_ar, $this->titleFontFamilyId, $this->titleFontFamilyId_ar,
                $this->headerPrimaryFontSize, $this->headerPrimaryFontSize_ar, $this->headerPrimaryFontFamilyId, $this->headerPrimaryFontFamilyId_ar,
                $this->headerSecondaryFontSize, $this->headerSecondaryFontSize_ar, $this->headerSecondaryFontFamilyId, $this->headerSecondaryFontFamilyId_ar,
                $this->bodyFontSize, $this->bodyFontSize_ar, $this->bodyFontFamilyId, $this->bodyFontFamilyId_ar,
                $this->tableHeaderFontSize, $this->tableHeaderFontSize_ar, $this->tableHeaderFontFamilyId, $this->tableHeaderFontFamilyId_ar,
                $this->tableRowsFontSize, $this->tableRowsFontSize_ar, $this->tableRowsFontFamilyId, $this->tableRowsFontFamilyId_ar,
                $this->footerFontSize, $this->footerFontSize_ar, $this->footerFontFamilyId, $this->footerFontFamilyId_ar,
                $this->documentSettingId
            );
            return true;
        } catch (Exception $e) {
            echo "Failed to save settings document: " . $e->getMessage();
            return false;
        }
    }

    // Create a new settings document
    public function create()
    {
        try {
            $query = "INSERT INTO settings_document 
                (titleFontSize, titleFontSize_ar, titleFontFamilyId, titleFontFamilyId_ar,
                headerPrimaryFontSize, headerPrimaryFontSize_ar, headerPrimaryFontFamilyId, headerPrimaryFontFamilyId_ar,
                headerSecondaryFontSize, headerSecondaryFontSize_ar, headerSecondaryFontFamilyId, headerSecondaryFontFamilyId_ar,
                bodyFontSize, bodyFontSize_ar, bodyFontFamilyId, bodyFontFamilyId_ar,
                tableHeaderFontSize, tableHeaderFontSize_ar, tableHeaderFontFamilyId, tableHeaderFontFamilyId_ar,
                tableRowsFontSize, tableRowsFontSize_ar, tableRowsFontFamilyId, tableRowsFontFamilyId_ar,
                footerFontSize, footerFontSize_ar, footerFontFamilyId, footerFontFamilyId_ar, createdAt)
                VALUES (?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, NOW())";

            $this->db->query($query,
                $this->titleFontSize, $this->titleFontSize_ar, $this->titleFontFamilyId, $this->titleFontFamilyId_ar,
                $this->headerPrimaryFontSize, $this->headerPrimaryFontSize_ar, $this->headerPrimaryFontFamilyId, $this->headerPrimaryFontFamilyId_ar,
                $this->headerSecondaryFontSize, $this->headerSecondaryFontSize_ar, $this->headerSecondaryFontFamilyId, $this->headerSecondaryFontFamilyId_ar,
                $this->bodyFontSize, $this->bodyFontSize_ar, $this->bodyFontFamilyId, $this->bodyFontFamilyId_ar,
                $this->tableHeaderFontSize, $this->tableHeaderFontSize_ar, $this->tableHeaderFontFamilyId, $this->tableHeaderFontFamilyId_ar,
                $this->tableRowsFontSize, $this->tableRowsFontSize_ar, $this->tableRowsFontFamilyId, $this->tableRowsFontFamilyId_ar,
                $this->footerFontSize, $this->footerFontSize_ar, $this->footerFontFamilyId, $this->footerFontFamilyId_ar
            );

            $this->documentSettingId = $this->db->insertId();
            return true;
        } catch (Exception $e) {
            echo "Failed to create settings document: " . $e->getMessage();
            return false;
        }
    }
}
