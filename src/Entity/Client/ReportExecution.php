<?php

namespace App\Entity\Client;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: 'report_executions')]
class ReportExecution
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer', options: ['unsigned' => true])]
    private ?int $id = null;

    #[ORM\ManyToOne(targetEntity: Report::class)]
    #[ORM\JoinColumn(name: 'report_id', referencedColumnName: 'id', nullable: false, onDelete: 'CASCADE')]
    private Report $report;

    #[ORM\Column(name: 'executed_at', type: Types::DATETIME_MUTABLE)]
    private \DateTimeInterface $executedAt;

    #[ORM\Column(type: 'string', length: 20)]
    private string $status;

    #[ORM\Column(type: 'boolean')]
    private bool $sended = false;

    #[ORM\Column(name: 'error_message', type: Types::TEXT, nullable: true)]
    private ?string $errorMessage = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReport(): Report
    {
        return $this->report;
    }

    public function setReport(Report $report): self
    {
        $this->report = $report;
        return $this;
    }

    public function getExecutedAt(): \DateTimeInterface
    {
        return $this->executedAt;
    }

    public function setExecutedAt(\DateTimeInterface $executedAt): self
    {
        $this->executedAt = $executedAt;
        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    public function isSended(): bool
    {
        return $this->sended;
    }

    public function setSended(bool $sended): self
    {
        $this->sended = $sended;
        return $this;
    }

    public function getErrorMessage(): ?string
    {
        return $this->errorMessage;
    }

    public function setErrorMessage(?string $errorMessage): self
    {
        $this->errorMessage = $errorMessage;
        return $this;
    }
}