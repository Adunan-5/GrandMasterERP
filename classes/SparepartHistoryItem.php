<?php

class SparepartHistoryItem
{
    private $db; // Database connection instance
    public  $sparepartId;
    public  $operationType;
    public  $stickerPottu;
    public  $title;
    public  $description;
    public  $updatedBy;
    public  $updatedAt;
    public  $attachmentLink;
    public  $updatedByImage;
    public  $updatedByDisplayName;
    public  $remark;
    public  $timeStamp;

    // Constructor to initialize the database connection
    public function __construct(string $sparepartId, string $operationType, string $remark, string $updatedBy, string $updatedAt)
    {
        global $db;
        $this->db = $db;

        $this->sparepartId    = $sparepartId;
        $this->operationType = $operationType;
        $this->remark        = $remark;
        $this->updatedBy     = $updatedBy;
        $this->updatedAt     = $updatedAt;

        $this->updatedAt = formatDateRelativeTime($this->updatedAt);

        $this->formTitle();
    }

    function stickerPottu()
    {

    }

    private function formTitle()
    {
        switch ($this->operationType) {
            case OT_NEW_SPAREPART:
                $this->title = "New Sparepart Created";
                $this->stickerPottu = "primary";
                break;

            case OT_EDIT_SPAREPART:
                $this->title = "Sparepart modified";
                $this->stickerPottu = "info";
                break;

            case OT_SPAREPART_QUANTITY_CHANGE:
                $this->title = "Sparepart quantity changed";
                $this->stickerPottu = "primary";
                break;

            default:
                $this->title = $this->operationType;
                break;
        }
    }


}