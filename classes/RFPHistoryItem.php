<?php

class RFPHistoryItem
{
    private $db; // Database connection instance
    public  $rfpId;
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
    public function __construct(string $rfpId, string $operationType, string $remark, string $updatedBy, string $updatedAt)
    {
        global $db;
        $this->db = $db;

        $this->rfpId    = $rfpId;
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
            case OT_NEW_RFP:
                $this->title = "New RFP Created";
                $this->stickerPottu = "primary";
                break;

            case OT_EDIT_RFP:
                $this->title = "RFP modified";
                $this->stickerPottu = "info";
                break;

            case OT_EDIT_RFP_WITH_ATTACHMENT:
                $this->title = "RFP modified with Supplier Quotation Info Attached";
                $this->stickerPottu = "info";
                break;

            case RFP_STATUS_PROCUREMENT_MANAGER_APPROVAL:
                $this->title = "RFP status awaiting approval";
                $this->stickerPottu = "warning";
                break;

            case RFP_STATUS_PROCUREMENT_MANAGER_APPROVED:
                $this->title = "RFP status procurement manager approved";
                $this->stickerPottu = "success";
                break;

            case RFP_STATUS_PROCUREMENT_MANAGER_REJECTED:
                $this->title = "RFP status procurement manager rejected";
                $this->stickerPottu = "danger";
                break;
                break;

            case RFP_STATUS_SENT_TO_SUPPLIER:
                $this->title = "RFP status sent to supplier";
                $this->stickerPottu = "info";
                break;

            case OT_PO_CREATED:
                $this->title = "PO Created for this RFP";
                $this->stickerPottu = "success";
                break;

            default:
                $this->title = $this->operationType;
                break;
        }
    }


}