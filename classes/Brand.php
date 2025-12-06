<?php

class Brand
{
    private $db; // SafeMySQL instance
    public $brandId;
    public $brandName;
    public $image;
    public $active;
    public $createdAt;
    public $updatedAt;

    // Constructor to initialize database instance
    public function __construct($db)
    {
        $this->db = $db; // Assign the SafeMySQL instance
    }

    // Method to load a brand by ID
    public function loadById($id)
    {
        try {
            $query = "SELECT * FROM brands WHERE brandId = ?i";
            $row = $this->db->getRow($query, $id);

            if ($row) {
                $this->setProperties($row);
                return true;
            } else {
                throw new Exception("Brand not found.");
            }
        } catch (Exception $e) {
            echo "Failed to load brand: " . $e->getMessage();
            return false;
        }
    }

    // Method to set properties from an array (e.g., database row)
    private function setProperties($row)
    {
        $this->brandId = $row['brandId'];
        $this->brandName = $row['brandName'];
        $this->image = $row['image'];
        $this->active = $row['active'];
        $this->createdAt = $row['createdAt'];
        $this->updatedAt = $row['updatedAt'];
    }

    // Method to save changes back to the database
    public function save()
    {
        try {
            $query = "UPDATE brands SET 
                brandName = ?s,
                image = ?s,
                updatedAt = NOW()
                WHERE brandId = ?i";

            $this->db->query($query,
                $this->brandName,
                $this->image,
                $this->brandId
            );
            return true;
        } catch (Exception $e) {
            echo "Failed to save brand: " . $e->getMessage();
            return false;
        }
    }

    // Method to create a new brand
    public function create()
    {
        try {
            $query = "INSERT INTO brands 
                (brandName, image, createdAt)
                VALUES (?s, ?s, NOW())";

            $this->db->query($query,
                $this->brandName,
                $this->image
            );

            $this->brandId = $this->db->insertId();
            return true;
        } catch (Exception $e) {
            echo "Failed to create brand: " . $e->getMessage();
            return false;
        }
    }
}
