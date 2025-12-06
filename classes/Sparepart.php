<?php

class Sparepart
{
    private $db; // SafeMySQL instance
    public $sparepartId;
    public $partNumber;
    public $serialNumber;
    public $internalReference;
    public $name;
    public $image;
    public $description;
    public $brandId;
    public $brandName; // Fetched from the brands table
    public $leadTime;
    public $origin;
    public $salesPrice;
    public $uomId;
    public $uomName; // Fetched from the uoms table
    public $cost;
    public $hsCode;
    public $hsPercentage;
    public $publishedOnEcommerce;
    public $notes; // New field for additional notes
    public $createdAt;
    public $updatedAt;

    // Constructor to initialize database instance
    public function __construct($db)
    {
        $this->db = $db; // Assign the SafeMySQL instance
    }

    // Method to load a spare part by ID
    public function loadById($id)
    {
        try {
            $query = "SELECT * FROM spareparts WHERE sparepartId = ?i";
            $row = $this->db->getRow($query, $id);

            if ($row) {
                $this->setProperties($row);
                $this->fetchRelatedData();
                return true;
            } else {
                throw new Exception("Spare part not found.");
            }
        } catch (Exception $e) {
            echo "Failed to load spare part: " . $e->getMessage();
            return false;
        }
    }

    // Method to set properties from an array (e.g., database row)
    private function setProperties($row)
    {
        $this->sparepartId = $row['sparepartId'];
        $this->partNumber = $row['partNumber'];
        $this->serialNumber = $row['serialNumber'];
        $this->internalReference = $row['internalReference'];
        $this->name = $row['name'];
        $this->image = $row['image'];
        $this->description = $row['description'];
        $this->brandId = $row['brandId'];
        $this->leadTime = $row['leadTime'];
        $this->origin = $row['origin'];
        $this->salesPrice = $row['salesPrice'];
        $this->uomId = $row['uomId'];
        $this->cost = $row['cost'];
        $this->hsCode = $row['hsCode'];
        $this->hsPercentage = $row['hsPercentage'];
        $this->publishedOnEcommerce = $row['publishedOnEcommerce'];
        $this->notes = $row['notes']; // Set notes property
        $this->createdAt = $row['createdAt'];
        $this->updatedAt = $row['updatedAt'];
    }

    // Method to fetch related data from the brands and uoms tables
    private function fetchRelatedData()
    {
        try {
            $brandQuery = "SELECT brandName FROM brands WHERE brandId = ?i";
            $brandRow = $this->db->getRow($brandQuery, $this->brandId);
            if ($brandRow) {
                $this->brandName = $brandRow['brandName'];
            }

            $uomQuery = "SELECT uomName FROM uoms WHERE uomId = ?i";
            $uomRow = $this->db->getRow($uomQuery, $this->uomId);
            if ($uomRow) {
                $this->uomName = $uomRow['uomName'];
            }
        } catch (Exception $e) {
            echo "Failed to fetch related data: " . $e->getMessage();
        }
    }

    // Method to save changes back to the database
    public function save()
    {
        try {
            $query = "UPDATE spareparts SET 
                partNumber = ?s,
                internalReference = ?s,
                name = ?s,
                image = ?s,
                description = ?s,
                brandId = ?i,
                leadTime = ?s,
                origin = ?s,
                salesPrice = ?s,
                uomId = ?i,
                cost = ?s,
                hsCode = ?s,
                hsPercentage = ?s,
                publishedOnEcommerce = ?i,
                notes = ?s, // Updated to include notes field
                updatedAt = NOW()
                WHERE sparepartId = ?i";

            $this->db->query($query,
                $this->partNumber,
                $this->internalReference,
                $this->name,
                $this->image,
                $this->description,
                $this->brandId,
                $this->leadTime,
                $this->origin,
                $this->salesPrice,
                $this->uomId,
                $this->cost,
                $this->hsCode,
                $this->hsPercentage,
                $this->publishedOnEcommerce,
                $this->notes,
                $this->sparepartId
            );
            return true;
        } catch (Exception $e) {
            echo "Failed to save spare part: " . $e->getMessage();
            return false;
        }
    }

    // Method to create a new spare part
    public function create()
    {
        try {
            $query = "INSERT INTO spareparts 
                (partNumber, internalReference, name, image, description, brandId, leadTime, origin, salesPrice, uomId, cost, hsCode, hsPercentage, publishedOnEcommerce, notes, createdAt)
                VALUES (?s, ?s, ?s, ?s, ?s, ?i, ?s, ?s, ?s, ?i, ?s, ?s, ?s, ?i, ?s, NOW())";

            $this->db->query($query,
                $this->partNumber,
                $this->internalReference,
                $this->name,
                $this->image,
                $this->description,
                $this->brandId,
                $this->leadTime,
                $this->origin,
                $this->salesPrice,
                $this->uomId,
                $this->cost,
                $this->hsCode,
                $this->hsPercentage,
                $this->publishedOnEcommerce,
                $this->notes
            );

            $this->sparepartId = $this->db->insertId();
            return true;
        } catch (Exception $e) {
            echo "Failed to create spare part: " . $e->getMessage();
            return false;
        }
    }
}
