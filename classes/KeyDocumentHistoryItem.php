<?php

class KeyDocumentHistoryItem
{
    private $db; // Database connection instance
    public  $documentId;
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
    public function __construct(string $documentId, string $operationType, string $remark, string $updatedBy, string $updatedAt)
    {
        global $db;
        $this->db = $db;

        $this->documentId    = $documentId;
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
            case OT_NEW_QUOTATION:
                $this->title = "New Quotation Created";
                $this->stickerPottu = "primary";
                break;

            case OT_SAVE_QUOTATION:
                $this->title = "Quotation modified";
                $this->stickerPottu = "info";
                break;

            case OT_SAVE_QUOTATION_WITH_ATTACHMENT:
                $this->title = "Quotation modified with PO Attached";
                $this->stickerPottu = "info";
                break;

            case QUOTATION_STATUS_AWAITING_APPROVAL:
                $this->title = "Quotation status awaiting approval";
                $this->stickerPottu = "warning";
                break;

            case QUOTATION_STATUS_APPROVED:
                $this->title = "Quotation Approved";
                $this->stickerPottu = "success";
                break;

            case QUOTATION_STATUS_REJECTED:
                $this->title = "Quotation Rejected";
                $this->stickerPottu = "danger";
                break;

            case QUOTATION_STATUS_SENT_TO_CUSTOMER:
                $this->title = "Quotation Sent to Customer";
                $this->stickerPottu = "info";
                break;

            case QUOTATION_STATUS_CUSTOMER_ACCEPTED:
                $this->title = "Customer Accepted";
                $this->stickerPottu = "success";
                break;

            case QUOTATION_STATUS_CUSTOMER_REJECTED:
                $this->title = "Customer Rejected";
                $this->stickerPottu = "danger";
                break;

            case QUOTATION_STATUS_CONFIRMED:
                $this->title = "Quotation Confirmed";
                $this->stickerPottu = "success";
                break;

            case QUOTATION_STATUS_CANCELLED:
                $this->title = "Quotation Cancelled";
                $this->stickerPottu = "danger";
                break;

            case QUOTATION_STATUS_AWAITING_SALES_MANAGER_SO_APPROVAL:
                $this->title = "Quotation Awaiting sales manager approval";
                $this->stickerPottu = "info";
                break;

            case QUOTATION_STATUS_SALES_MANAGER_SO_REJECTED:
                $this->title = "Quotation Sales Manager Rejected";
                $this->stickerPottu = "danger";
                break;

            case QUOTATION_STATUS_AWAITING_ACCOUNTANT_SO_APPROVAL:
                $this->title = "Quotation Awaiting accountant approval";
                $this->stickerPottu = "info";
                break;

            case QUOTATION_STATUS_SO_APPROVED:
                $this->title = "Quotation Sales Manager Approved";
                $this->stickerPottu = "success";
                break;

            default:
                $this->title = $this->operationType;
                break;
        }
    }


}