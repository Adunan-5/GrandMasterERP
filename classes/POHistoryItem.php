<?php

class POHistoryItem
{
    private $db; // Database connection instance
    public  $poId;
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
    public function __construct(string $poId, string $operationType, string $remark, string $updatedBy, string $updatedAt)
    {
        global $db;
        $this->db = $db;

        $this->poId    = $poId;
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
            case OT_NEW_PO:
                $this->title = "New PO Created";
                $this->stickerPottu = "primary";
                break;

            case OT_EDIT_PO:
                $this->title = "PO modified";
                $this->stickerPottu = "info";
                break;
            
            case OT_EDIT_PO_WITH_PAYMENT_ATTACHMENT:
                $this->title = "PO modified with Payment Reference Info Attached";
                $this->stickerPottu = "info";
                break;

            case OT_EDIT_PO_INVOICE_ATTACHMENT:
                $this->title = "PO modified with Supplier Invoice Info Attached";
                $this->stickerPottu = "info";
                break;

            case PO_STATUS_AWAITING_SPAREPARTS_MANAGER_APPROVAL:
                $this->title = "PO status awaiting Spareparts Manager approval";
                $this->stickerPottu = "warning";
                break;

            case PO_STATUS_SPAREPARTS_MANAGER_APPROVED:
                $this->title = "PO status Spareparts Manager approved";
                $this->stickerPottu = "success";
                break;

            case PO_STATUS_SPAREPARTS_MANAGER_REJECTED:
                $this->title = "PO status Spareparts Manager rejected";
                $this->stickerPottu = "danger";
                break;

            case PO_STATUS_AWAITING_ACCOUNTANT_APPROVAL:
                $this->title = "PO status awaiting Accountant approval";
                $this->stickerPottu = "warning";
                break;

            case PO_STATUS_ACCOUNTANT_APPROVED:
                $this->title = "PO status Accountant approved";
                $this->stickerPottu = "success";
                break;

            case PO_STATUS_ACCOUNTANT_REJECTED:
                $this->title = "PO status Accountant rejected";
                $this->stickerPottu = "danger";
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