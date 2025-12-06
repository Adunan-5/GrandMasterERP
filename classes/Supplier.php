<?php

class Supplier
{
    private $db; // SafeMySQL instance
    public $supplierId;
    public $companyName;
    public $companyNameAr;
    public $companyCRNumber;
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
    public $customerCode;
    public $logoUrl;
    public $salesPaymentTermId;
    public $purchasePaymentTermId;
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
            $query = "SELECT * FROM suppliers WHERE supplierId = ?i";
            $row = $this->db->getRow($query, $id);

            if ($row) {
                $this->setProperties($row);
                return true;
            } else {
                throw new Exception("Supplier not found.");
            }
        } catch (Exception $e) {
            echo "Failed to load supplier: " . $e->getMessage();
            return false;
        }
    }

    // Method to set properties from an array (e.g., database row)
    private function setProperties($row)
    {
        $this->supplierId = $row['supplierId'];
        $this->companyName = $row['companyName'];
        $this->companyNameAr = $row['companyNameAr'];
        $this->companyCRNumber = $row['companyCRNumber'];
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
        $this->customerCode = $row['customerCode'];
        $this->logoUrl = $row['logoUrl'];
        $this->salesPaymentTermId = $row['salesPaymentTermId'];
        $this->purchasePaymentTermId = $row['purchasePaymentTermId'];
        $this->currencyId = $row['currencyId'];
        $this->createdAt = $row['createdAt'];
        $this->updatedAt = $row['updatedAt'];
        $this->status = $row['status'];
    }

    // Method to save changes back to the database
    public function save()
    {
        try {
            $query = "UPDATE suppliers SET                
                companyName = ?s,
                companyNameAr = ?s,
                companyCRNumber = ?s,
                addressLine1 = ?s,
                addressLine2 = ?s,
                cityId = ?i,
                stateId = ?i,
                postalCode = ?s,
                countryId = ?i,
                vatNumber = ?s,
                phone = ?s,
                mobile = ?s,
                email = ?s,
                website = ?s,
                tags = ?s,
                customerCode = ?s,
                logoUrl = ?s,
                salesPaymentTermId = ?i,
                purchasePaymentTermId = ?i,
                currencyId = ?i,
                updatedAt = NOW(),
                status = ?s
                WHERE supplierId = ?i";

            $this->db->query($query,
                $this->companyName,
                $this->companyNameAr,
                $this->companyCRNumber,
                $this->addressLine1,
                $this->addressLine2,
                $this->cityId,
                $this->stateId,
                $this->postalCode,
                $this->countryId,
                $this->vatNumber,
                $this->phone,
                $this->mobile,
                $this->email,
                $this->website,
                $this->tags,
                $this->customerCode,
                $this->logoUrl,
                $this->salesPaymentTermId,
                $this->purchasePaymentTermId,
                $this->currencyId,
                $this->status,
                $this->customerId
            );
            return true;
        } catch (Exception $e) {
            echo "Failed to save supplier: " . $e->getMessage();
            return false;
        }
    }

    // Method to create a new customer
    public function create()
    {
        try {
            $query = "INSERT INTO suppliers 
                ( companyName, companyNameAr,companyCRNumber, addressLine1, addressLine2, cityId, stateId, postalCode, countryId, vatNumber, phone, mobile, email, website, tags, customerCode, logoUrl, salesPaymentTermId, purchasePaymentTermId, currencyId, createdAt, status)
                VALUES ( ?s, ?s, ?s,?s, ?s, ?i, ?i, ?s, ?i, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?i, ?i, ?i, NOW(), ?s)";

            $this->db->query($query,
                $this->companyName,
                $this->companyNameAr,
                $this->companyCRNumber,
                $this->addressLine1,
                $this->addressLine2,
                $this->cityId,
                $this->stateId,
                $this->postalCode,
                $this->countryId,
                $this->vatNumber,
                $this->phone,
                $this->mobile,
                $this->email,
                $this->website,
                $this->tags,
                $this->customerCode,
                $this->logoUrl,
                $this->salesPaymentTermId,
                $this->purchasePaymentTermId,
                $this->currencyId,
                $this->status
            );

            $this->supplierId = $this->db->insertId();
            return true;
        } catch (Exception $e) {
            echo "Failed to create supplier: " . $e->getMessage();
            return false;
        }
    }


}