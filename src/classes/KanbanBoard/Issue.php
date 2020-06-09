<?php declare(strict_types=1);

namespace App\KanbanBoard;

class Issue
{
    const STATE_ALL = 'all';
    const STATE_OPEN = 'open';
    const STATE_CLOSED = 'closed';

    private int $id;
    private string $title;
    private array $labels;
    private string $state;
    private ?array $assignee;
    private ?array $assignees;
    private \DateTime $createdAt;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     */
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * @param string $title
     */
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return array
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * @param array $labels
     */
    public function setLabels(array $labels): void
    {
        $this->labels = $labels;
    }

    /**
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * @param string $state
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * @return array|null
     */
    public function getAssignee(): ?array
    {
        return $this->assignee;
    }

    /**
     * @param array $assignee
     */
    public function setAssignee(?array $assignee): void
    {
        $this->assignee = $assignee;
    }

    /**
     * @return array
     */
    public function getAssignees(): ?array
    {
        return $this->assignees;
    }

    /**
     * @param array $assignees
     */
    public function setAssignees(?array $assignees): void
    {
        $this->assignees = $assignees;
    }

    /**
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    public function isAssigned(): bool {
        return (!empty($this->assignee) || !empty($this->assignees));
    }

    public static function createFromGithubResponse(array $githubResponseIssue): Issue {
        $issue = new Issue();
        $issue->setId($githubResponseIssue['id']);
        $issue->setTitle($githubResponseIssue['title']);
        $issue->setLabels($githubResponseIssue['labels']);
        $issue->setState($githubResponseIssue['state']);
        $issue->setAssignee($githubResponseIssue['assignee']);
        $issue->setAssignees($githubResponseIssue['assignees']);
        $issue->setCreatedAt(new \DateTime($githubResponseIssue['created_at']));
        return $issue;
    }
}