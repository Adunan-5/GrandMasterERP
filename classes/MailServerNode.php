<?php

class MailServerNode {
    public $serverNodeID;
    public $label;
    public $description;
    public $tags;
    public $serverNodeIP;
    public $dbHostName;
    public $dbName;
    public $dbUserName;
    public $dbPassword;
    public $dbPort;
    public $dateCreated;
    public $dateModified;
    public $api;
    public $active;

    // Constructor to initialize the properties
    public function __construct($data) {
        $this->serverNodeID = $data['serverNodeID'];
        $this->label = $data['label'];
        $this->description = $data['description'];
        $this->tags = $data['tags'];
        $this->serverNodeIP = $data['serverNodeIP'];
        $this->dbHostName = $data['dbHostName'];
        $this->dbName = $data['dbName'];
        $this->dbUserName = $data['dbUserName'];
        $this->dbPassword = $data['dbPassword'];
        $this->dbPort = $data['dbPort'];
        $this->dateCreated = $data['dateCreated'];
        $this->dateModified = $data['dateModified'];
        $this->api = $data['api'];
        $this->active = $data['active'];
    }

    // Example method to display basic information about the server node
    public function getSummary() {
        return "Mail Server Node: {$this->label} (IP: {$this->serverNodeIP})";
    }
}