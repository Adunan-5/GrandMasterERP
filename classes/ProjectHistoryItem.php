<?php

class ProjectHistoryItem
{
    private $db; // Database connection instance
    public  $projectId;
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
    public function __construct(string $projectId, string $operationType, string $remark, string $updatedBy, string $updatedAt)
    {
        global $db;
        $this->db = $db;

        $this->projectId    = $projectId;
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
            case OT_NEW_PROJECT:
                $this->title = "New Project Created";
                $this->stickerPottu = "primary";
                break;

            case OT_EDIT_PROJECT:
                $this->title = "Project modified";
                $this->stickerPottu = "info";
                break;

            case OT_NEW_TASK:
                $this->title = "New Task Created";
                $this->stickerPottu = "primary";
                break;

            case OT_EDIT_TASK:
                $this->title = "Task modified";
                $this->stickerPottu = "info";
                break;

            case OT_MARK_TASK_DONE:
                $this->title = "Task completed";
                $this->stickerPottu = "success";
                break;

            case OT_DELETE_TASK:
                $this->title = "Task deleted";
                $this->stickerPottu = "danger";
                break;

            default:
                $this->title = $this->operationType;
                break;
        }
    }


}