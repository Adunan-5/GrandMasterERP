<?php

class AuthenticatedUser
{
    public $userID;
    public $userName;
    public $firstName;
    public $lastName;
    public $profilePic;
    public $email;
    public $dateCreated;
    public $dateModified;
    public $active;

    public $roles;

    public function __construct($data, $roles)
    {
        $this->userID = $data['userID'];
        $this->userName = $data['userName'];
        $this->firstName = $data['firstName'];
        $this->lastName = $data['lastName'];
        $this->profilePic = $data['profilePic'];
        $this->email = $data['email'];
        $this->dateCreated = $data['dateCreated'];
        $this->dateModified = $data['dateModified'];
        $this->active = $data['active'];

        $this->roles = $roles;
    }

    public function getFullName()
    {
        return $this->firstName . " " . $this->lastName;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function getRoleName()
    {
        global $db;
        $res = $db->query("SELECT * FROM user_roles WHERE roleId = ?i", $this->roles[0]);
        while($row=mysqli_fetch_assoc($res))
        {
        	return $row['roleName'];
        }

        return "";
    }

    public function hasRole($role)
    {
        return in_array($role, $this->roles, true);
    }

    public function getUserCompanies(){
        global $db;
        $res = $db->query("SELECT
  `companies`.`status`,
  `companies`.`companyName`,
  `companies`.`companyLabel`,
  `companies`.`companyNameAr`,
  `companies`.`addressLine1`,
  `companies`.`addressLine2`,
  `companies`.`cityId`,
  `companies`.`stateId`,
  `companies`.`postalCode`,
  `companies`.`countryId`,
  `companies`.`vatNumber`,
  `companies`.`phone`,
  `companies`.`mobile`,
  `companies`.`email`,
  `companies`.`website`,
  `companies`.`tags`,
  `companies`.`companyCRNumber`,
  `companies`.`logoUrl`,
  `companies`.`currencyId`,
  `companies`.`createdAt`,
  `companies`.`updatedAt`,
  `user_company_mapping`.`userId` AS `userId`,
  `user_company_mapping`.`companyId` AS `companyId`,
  `user_company_mapping`.`mappingId`
FROM
  `user_company_mapping`
  LEFT JOIN `companies` ON `user_company_mapping`.`companyId` = `companies`.`companyId`
Where `userId` = ?s", $this->userID );

        $returnData = array();
        while($row=mysqli_fetch_assoc($res))
        {
            array_push($returnData, $row);
        }

        return $returnData;
    }

}