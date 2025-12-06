<?php

class Warehouse
{
    private $db; // SafeMySQL instance
    public $warehouseId;
    public $warehouseName;
    public $warehouseCode;
    public $addressLine1;
    public $addressLine2;
    public $cityId;
    public $stateId;
    public $postalCode;
    public $countryId;
    public $phone;
    public $mobile;
    public $email;
    public $active;
    public $dateCreated;
    public $dateUpdated;

    // Constructor to initialize database instance
    public function __construct()
    {
        global $db;
        $this->db = $db;
    }

    // Method to load a warehouse by ID
    public function loadById($id)
    {
        try {
            $query = "SELECT * FROM warehouses WHERE warehouseId = ?i";
            $row = $this->db->getRow($query, $id);

            if ($row) {
                $this->setProperties($row);
                return true;
            } else {
                throw new Exception("Warehouse not found.");
            }
        } catch (Exception $e) {
            echo "Failed to load warehouse: " . $e->getMessage();
            return false;
        }
    }

    // Method to set properties from an array (e.g., database row)
    private function setProperties($row)
    {
        $this->warehouseId = $row['warehouseId'];
        $this->warehouseName = $row['warehouseName'];
        $this->warehouseCode = $row['warehouseCode'];
        $this->addressLine1 = $row['addressLine1'];
        $this->addressLine2 = $row['addressLine2'];
        $this->cityId = $row['cityId'];
        $this->stateId = $row['stateId'];
        $this->postalCode = $row['postalCode'];
        $this->countryId = $row['countryId'];
        $this->phone = $row['phone'];
        $this->mobile = $row['mobile'];
        $this->email = $row['email'];
        $this->active = $row['active'];
        $this->dateCreated = $row['dateCreated'];
        $this->dateUpdated = $row['dateUpdated'];
    }

    // Method to save changes back to the database
    public function save()
    {
        try {
            $query = "UPDATE warehouses SET 
                warehouseName = ?s,
                warehouseCode = ?s,
                addressLine1 = ?s,
                addressLine2 = ?s,
                cityId = ?i,
                stateId = ?i,
                postalCode = ?s,
                countryId = ?i,
                phone = ?s,
                mobile = ?s,
                email = ?s,
                active = ?i,
                dateUpdated = NOW()
                WHERE warehouseId = ?i";

            $this->db->query($query,
                $this->warehouseName,
                $this->warehouseCode,
                $this->addressLine1,
                $this->addressLine2,
                $this->cityId,
                $this->stateId,
                $this->postalCode,
                $this->countryId,
                $this->phone,
                $this->mobile,
                $this->email,
                $this->active,
                $this->warehouseId
            );
            return true;
        } catch (Exception $e) {
            echo "Failed to save warehouse: " . $e->getMessage();
            return false;
        }
    }

    // Method to create a new warehouse
    public function create()
    {
        try {
            $query = "INSERT INTO warehouses 
                (warehouseName, warehouseCode, addressLine1, addressLine2, cityId, stateId, postalCode, countryId, phone, mobile, email, active, dateCreated) 
                VALUES (?s, ?s, ?s, ?s, ?i, ?i, ?s, ?i, ?s, ?s, ?s, ?i, NOW())";

            $this->db->query($query,
                $this->warehouseName,
                $this->warehouseCode,
                $this->addressLine1,
                $this->addressLine2,
                $this->cityId,
                $this->stateId,
                $this->postalCode,
                $this->countryId,
                $this->phone,
                $this->mobile,
                $this->email,
                $this->active
            );

            $this->warehouseId = $this->db->insertId();
            return true;
        } catch (Exception $e) {
            echo "Failed to create warehouse: " . $e->getMessage();
            return false;
        }
    }
}
