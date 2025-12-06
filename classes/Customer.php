<?php

class Customer
{
    private $db; // SafeMySQL instance
    public $customerId;
    public $customerType;
    public $companyName;
    public $companyNameAr;
    public $companyCRNumber;
    public $companyCRFileName;
    public $addressLine1;
    public $addressLine2;
    public $cityId;
    public $stateId;
    public $postalCode;
    public $countryId;
    public $vatNumber;
    public $vatFileName;
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

    public $ecommerceAddress = array();

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
            $query = "SELECT * FROM customers WHERE customerId = ?i";
            $row = $this->db->getRow($query, $id);

            if ($row) {
                $this->setProperties($row);
                $this->loadEcommerceAddresses();
                return true;
            } else {
                throw new Exception("Customer not found.");
            }
        } catch (Exception $e) {
            echo "Failed to load customer: " . $e->getMessage();
            return false;
        }
    }
    // Method to load ecommerce addresses for this customer
    private function loadEcommerceAddresses()
    {
        try {
            $query = "SELECT 
                        addressLine1, addressLine2, city, state, 
                        country, postalCode, isBilling 
                      FROM ecommerce_customers_addresses 
                      WHERE customerId = ?i";
            $this->ecommerceAddress = $this->db->getAll($query, $this->customerId);
        } catch (Exception $e) {
            echo "Failed to load ecommerce addresses: " . $e->getMessage();
        }
    }

    // Method to set properties from an array (e.g., database row)
    private function setProperties($row)
    {
        $this->customerId = $row['customerId'];
        $this->customerType = $row['customerType'];
        $this->companyName = $row['companyName'];
        $this->companyNameAr = $row['companyNameAr'];
        $this->companyCRNumber = $row['companyCRNumber'];
        $this->companyCRFileName = $row['companyCRFileName'];
        $this->addressLine1 = $row['addressLine1'];
        $this->addressLine2 = $row['addressLine2'];
        $this->cityId = $row['cityId'];
        $this->stateId = $row['stateId'];
        $this->postalCode = $row['postalCode'];
        $this->countryId = $row['countryId'];
        $this->vatNumber = $row['vatNumber'];
        $this->vatFileName = $row['vatFileName'];
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
            $query = "UPDATE customers SET 
                customerType = ?s,
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
                WHERE customerId = ?i";

            $this->db->query($query,
                $this->customerType,
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
            echo "Failed to save customer: " . $e->getMessage();
            return false;
        }
    }

    // Method to create a new customer
    public function create()
    {
        try {
            $query = "INSERT INTO customers 
                (customerType, companyName, companyNameAr,companyCRNumber, addressLine1, addressLine2, cityId, stateId, postalCode, countryId, vatNumber, phone, mobile, email, website, tags, customerCode, logoUrl, salesPaymentTermId, purchasePaymentTermId, currencyId, createdAt, status)
                VALUES (?s, ?s, ?s, ?s,?s, ?s, ?i, ?i, ?s, ?i, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?s, ?i, ?i, ?i, NOW(), ?s)";

            $this->db->query($query,
                $this->customerType,
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

            $this->customerId = $this->db->insertId();
            return true;
        } catch (Exception $e) {
            echo "Failed to create customer: " . $e->getMessage();
            return false;
        }
    }


}