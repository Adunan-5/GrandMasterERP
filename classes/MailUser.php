<?php

class MailUser
{


    public $username;
    public $password;
    public $name;
    public $language;
    public $mailboxformat;
    public $mailboxfolder;
    public $storagebasedirectory;
    public $storagenode;
    public $maildir;
    public $quota;
    public $domain;
    public $transport;
    public $department;
    public $rank;
    public $employeeid;
    public $isadmin;
    public $isglobaladmin;
    public $enablesmtp;
    public $enablesmtpsecured;
    public $enablepop3;
    public $enablepop3secured;
    public $enablepop3tls;
    public $enableimap;
    public $enableimapsecured;
    public $enableimaptls;
    public $enabledeliver;
    public $enablelda;
    public $enablemanagesieve;
    public $enablemanagesievesecured;
    public $enablesieve;
    public $enablesievesecured;
    public $enablesievetls;
    public $enableinternal;
    public $enabledoveadm;
    public $enablelibstorage;
    public $enablequotastatus;
    public $enableindexerworker;
    public $enablelmtp;
    public $enabledsync;
    public $enablesogo;
    public $enablesogowebmail;
    public $enablesogocalendar;
    public $enablesogoactivesync;
    public $allow_nets;
    public $disclaimer;
    public $settings;
    public $passwordlastchange;
    public $created;
    public $modified;
    public $expired;
    public $active;


    public function __construct($data)
    {
        $this->username = $data['username'];
        $this->password = $data['password'];
        $this->name = $data['name'];
        $this->language = $data['language'];
        $this->mailboxformat = $data['mailboxformat'];
        $this->mailboxfolder = $data['mailboxfolder'];
        $this->storagebasedirectory = $data['storagebasedirectory'];
        $this->storagenode = $data['storagenode'];
        $this->maildir = $data['maildir'];
        $this->quota = $data['quota'];
        $this->domain = $data['domain'];
        $this->transport = $data['transport'];
        $this->department = $data['department'];
        $this->rank = $data['rank'];
        $this->employeeid = $data['employeeid'];
        $this->isadmin = $data['isadmin'];
        $this->isglobaladmin = $data['isglobaladmin'];
        $this->enablesmtp = $data['enablesmtp'];
        $this->enablesmtpsecured = $data['enablesmtpsecured'];
        $this->enablepop3 = $data['enablepop3'];
        $this->enablepop3secured = $data['enablepop3secured'];
        $this->enablepop3tls = $data['enablepop3tls'];
        $this->enableimap = $data['enableimap'];
        $this->enableimapsecured = $data['enableimapsecured'];
        $this->enableimaptls = $data['enableimaptls'];
        $this->enabledeliver = $data['enabledeliver'];
        $this->enablelda = $data['enablelda'];
        $this->enablemanagesieve = $data['enablemanagesieve'];
        $this->enablemanagesievesecured = $data['enablemanagesievesecured'];
        $this->enablesieve = $data['enablesieve'];
        $this->enablesievesecured = $data['enablesievesecured'];
        $this->enablesievetls = $data['enablesievetls'];
        $this->enableinternal = $data['enableinternal'];
        $this->enabledoveadm = $data['enabledoveadm'];
        $this->enablelibstorage = $data['enablelib-storage'];
        $this->enablequotastatus = $data['enablequota-status'];
        $this->enableindexerworker = $data['enableindexer-worker'];
        $this->enablelmtp = $data['enablelmtp'];
        $this->enabledsync = $data['enabledsync'];
        $this->enablesogo = $data['enablesogo'];
        $this->enablesogowebmail = $data['enablesogowebmail'];
        $this->enablesogocalendar = $data['enablesogocalendar'];
        $this->enablesogoactivesync = $data['enablesogoactivesync'];
        $this->allow_nets = $data['allow_nets'];
        $this->disclaimer = $data['disclaimer'];
        $this->settings = $data['settings'];
        $this->passwordlastchange = $data['passwordlastchange'];
        $this->created = $data['created'];
        $this->modified = $data['modified'];
        $this->expired = $data['expired'];
        $this->active = $data['active'];
    }
}