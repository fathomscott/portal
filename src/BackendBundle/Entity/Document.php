<?php
namespace BackendBundle\Entity;

use Vich\UploaderBundle\Mapping\Annotation as Vich;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Document
 * @Vich\Uploadable
 */
class Document
{
    const STATUS_PENDING = 0;
    const STATUS_VALID = 1;
    const STATUS_EXPIRED  = 2;
    const STATUS_DELETED  = 99;

    public static $statuses = array(
        'labels.pending' => self::STATUS_PENDING,
        'labels.valid'   => self::STATUS_VALID,
        'labels.expired' => self::STATUS_EXPIRED,
        'labels.deleted' => self::STATUS_DELETED,
    );

    /**
     * @var integer
     */
    private $id;

    /**
     * @var Agent
     */
    private $agent;

    /**
     * @var DocumentOption
     */
    private $documentOption;

    /**
     * @var integer
     */
    private $status = self::STATUS_PENDING;

    /**
     * @var \DateTime
     */
    private $uploadedDate;

    /**
     * @var \DateTime
     */
    private $expirationDate;

    /**
     * @Assert\File(mimeTypes={"application/pdf", "application/x-pdf", "application/msword", "application/vnd.openxmlformats-officedocument.wordprocessingml.document", "application/vnd.ms-word.document.macroEnabled.12"})
     *
     * @Vich\UploadableField(mapping="agent_document", fileNameProperty="documentName")
     *
     * @var File
     */
    private $documentFile;

    /**
     * @var string
     */
    private $documentName;

    /**
     * @var string
     */
    private $description;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Agent
     */
    public function getAgent()
    {
        return $this->agent;
    }

    /**
     * @param Agent $agent
     */
    public function setAgent($agent)
    {
        $this->agent = $agent;
    }

    /**
     * @return DocumentOption
     */
    public function getDocumentOption()
    {
        return $this->documentOption;
    }

    /**
     * @param DocumentOption $documentOption
     */
    public function setDocumentOption($documentOption)
    {
        $this->documentOption = $documentOption;
    }

    /**
     * @return int
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param int $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return string|null
     */
    public function getStatusName()
    {
        if (in_array($this->getStatus(), self::$statuses, true)) {
            return array_search($this->getStatus(), self::$statuses);
        }

        return null;
    }

    /**
     * @return File
     */
    public function getDocumentFile()
    {
        return $this->documentFile;
    }

    /**
     * @param File $documentFile
     */
    public function setDocumentFile($documentFile)
    {
        $this->documentFile = $documentFile;

        if ($documentFile) {
            $this->uploadedDate = new \DateTime('now');
        }
    }

    /**
     * @return string
     */
    public function getDocumentName()
    {
        return $this->documentName;
    }

    /**
     * @param string $documentName
     */
    public function setDocumentName($documentName)
    {
        $this->documentName = $documentName;
    }

    /**
     * @return \DateTime
     */
    public function getUploadedDate()
    {
        return $this->uploadedDate;
    }

    /**
     * @param \DateTime $uploadedDate
     */
    public function setUploadedDate($uploadedDate)
    {
        $this->uploadedDate = $uploadedDate;
    }

    /**
     * @return \DateTime
     */
    public function getExpirationDate()
    {
        return $this->expirationDate;
    }

    /**
     * @param \DateTime $expirationDate
     */
    public function setExpirationDate($expirationDate)
    {
        $this->expirationDate = $expirationDate;
    }

    /**
     * @return \String $description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param \String $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }
}
