<?php

class Company
{
    private $db; // SafeMySQL instance
    public $companyId;
    public $companyName;
    public $companyNameAr;
    public $addressLine1;
    public $addressLine2;
    public $cityId;
    public $stateId;
    public $postalCode;
    public $countryId;
    public $vatNumber;
    public $phone;
    public $mobile;
    public $email;
    public $website;
    public $tags;
    public $companyCRNumber;
    public $logoUrl;
    public $currencyId;
    public $createdAt;
    public $updatedAt;
    public $status;
    public $fullAddress;

    // Constructor to initialize database instance
    public function __construct()
    {
        global $db;
        $this->db = $db; // Assign the SafeMySQL instance
    }

    // Method to load a customer by ID
    public function loadById($id)
    {
        try {
            $query = "SELECT * FROM companies WHERE companyId = ?i";
            $row = $this->db->getRow($query, $id);

            if ($row) {
                $this->setProperties($row);
                return true;
            } else {
                throw new Exception("Company not found.");
            }
        } catch (Exception $e) {
            echo "Failed to load company: " . $e->getMessage();
            return false;
        }
    }

    // Method to set properties from an array (e.g., database row)
    private function setProperties($row)
    {



        $this->companyId = $row['companyId'];
        $this->companyName = $row['companyName'];
        $this->companyNameAr = $row['companyNameAr'];
        $this->addressLine1 = $row['addressLine1'];
        $this->addressLine2 = $row['addressLine2'];
        $this->cityId = $row['cityId'];
        $this->stateId = $row['stateId'];
        $this->postalCode = $row['postalCode'];
        $this->countryId = $row['countryId'];
        $this->vatNumber = $row['vatNumber'];
        $this->phone = $row['phone'];
        $this->mobile = $row['mobile'];
        $this->email = $row['email'];
        $this->website = $row['website'];
        $this->tags = $row['tags'];
        $this->companyCRNumber = $row['companyCRNumber'];
        $this->logoUrl = $row['logoUrl'];
        $this->currencyId = $row['currencyId'];
        $this->createdAt = $row['createdAt'];
        $this->updatedAt = $row['updatedAt'];
        $this->status = $row['status'];
    }

}