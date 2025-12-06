<?php

class User
{
    private $db; // SafeMySQL instance
    public $userID;
    public $employeeId;
    public $userName;
    public $firstName;
    public $lastName;
    public $profilePic;
    public $userContract;
    public $email;
    public $dateCreated;
    public $dateModified;
    public $active;
    public $roleId;
    public $roleName;
    public $companyIds = []; // Array to hold multiple company IDs
    public $companyLabels = []; // Array to hold company labels
    public $iqamaNumber;
    public $iqamaExpiry;
    public $passportNumber;
    public $passportExpiry;
    public $medicalInsuranceNumber;
    public $medicalInsuranceExpiry;
    public $medicalInsuranceCompany;
    public $contractStartDate;
    public $contractEndDate;
    public $countryId;
    public $workDaysPerMonth;
    public $hoursPerDay;
    public $basicSalary;
    public $otMultiplier;
    public $gosiCap;
    public $employerGOSI;
    public $employeeGOSI;

    // Constructor to initialize database instance
    public function __construct($db)
    {
        $this->db = $db;
    }

    // Method to load a user by ID, including associated companies
    public function loadById($id)
    {
        try {
            $query = "SELECT u.*, ur.roleName, ur.roleId, c.companyId, c.companyLabel
                      FROM users u
                      LEFT JOIN user_role_mapping urm ON u.userID = urm.userId
                      LEFT JOIN user_roles ur ON urm.roleId = ur.roleId
                      LEFT JOIN user_company_mapping ucm ON u.userID = ucm.userId
                      LEFT JOIN companies c ON ucm.companyId = c.companyId
                      WHERE u.userID = ?i";

            $rows = $this->db->getAll($query, $id);

            if ($rows) {
                // Set properties based on the first row (assuming user exists)
                $this->setProperties($rows[0]);

                // Now set the companyIds and companyLabels arrays
                foreach ($rows as $row) {
                    if ($row['companyId'] !== null) {
                        $this->companyIds[] = $row['companyId'];
                        $this->companyLabels[] = $row['companyLabel'];
                    }
                }

                return true;
            } else {
                throw new Exception("User not found.");
            }
        } catch (Exception $e) {
            echo "Failed to load user: " . $e->getMessage();
            return false;
        }
    }

    // Method to set properties from an array (e.g., database row)
    private function setProperties($row)
    {
        $this->userID = $row['userID'];
        $this->employeeId = $row['employeeId'];
        $this->userName = $row['userName'];
        $this->firstName = $row['firstName'];
        $this->lastName = $row['lastName'];
        $this->profilePic = $row['profilePic'];
        $this->userContract = $row['userContract'];
        $this->email = $row['email'];
        $this->dateCreated = $row['dateCreated'];
        $this->dateModified = $row['dateModified'];
        $this->active = $row['active'];
        $this->roleName = $row['roleName'] ?? null;
        $this->roleId = $row['roleId'] ?? null;
        $this->iqamaNumber = $row['iqamaNumber'];
        $this->iqamaExpiry = $row['iqamaExpiry'];
        $this->passportNumber = $row['passportNumber'];
        $this->passportExpiry = $row['passportExpiry'];
        $this->medicalInsuranceNumber = $row['medicalInsuranceNumber'];
        $this->medicalInsuranceExpiry = $row['medicalInsuranceExpiry'];
        $this->medicalInsuranceCompany = $row['medicalInsuranceCompany'];
        $this->contractStartDate = $row['contractStartDate'];
        $this->contractEndDate = $row['contractEndDate'];
        $this->countryId = $row['countryId'];
        $this->workDaysPerMonth = $row['workDaysPerMonth'];
        $this->hoursPerDay = $row['hoursPerDay'];
        $this->basicSalary = $row['basicSalary'];
        $this->otMultiplier = $row['otMultiplier'];
        $this->gosiCap = $row['gosiCap'];
        $this->employerGOSI = $row['employerGOSI'];
        $this->employeeGOSI = $row['employeeGOSI'];
    }

    // Method to save changes back to the database
    public function save()
    {
        try {
            // Update user details in the `users` table
            $query = "UPDATE users SET 
                userName = ?s,
                firstName = ?s,
                lastName = ?s,
                profilePic = ?s,
                email = ?s,
                dateModified = NOW(),
                active = ?i
                WHERE userID = ?i";

            $this->db->query($query,
                $this->userName,
                $this->firstName,
                $this->lastName,
                $this->profilePic,
                $this->email,
                $this->active,
                $this->userID
            );

            // Now, handle the companies (update or insert the relationships in user_company_mapping)
            $this->updateCompanies();

            return true;
        } catch (Exception $e) {
            echo "Failed to save user: " . $e->getMessage();
            return false;
        }
    }

    // Method to update the company associations for the user
    private function updateCompanies()
    {
        // Clear old company mappings
        $this->db->query("DELETE FROM user_company_mapping WHERE userId = ?i", $this->userID);

        // Insert new company mappings if any
        if (!empty($this->companyIds)) {
            foreach ($this->companyIds as $companyId) {
                $this->db->query("INSERT INTO user_company_mapping (userId, companyId) VALUES (?i, ?i)", $this->userID, $companyId);
            }
        }
    }

    // Method to create a new user (also handle companies)
    public function create()
    {
        try {
            // Create the user record
            $query = "INSERT INTO users 
                (userName, firstName, lastName, profilePic, email, dateCreated, active)
                VALUES (?s, ?s, ?s, ?s, ?s, NOW(), ?i)";

            $this->db->query($query,
                $this->userName,
                $this->firstName,
                $this->lastName,
                $this->profilePic,
                $this->email,
                $this->active
            );

            // Get the newly created user's ID
            $this->userID = $this->db->insertId();

            // Now, handle the companies for this user
            $this->updateCompanies();

            return true;
        } catch (Exception $e) {
            echo "Failed to create user: " . $e->getMessage();
            return false;
        }
    }
}
