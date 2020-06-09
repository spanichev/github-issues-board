<?php declare(strict_types=1);

namespace App\KanbanBoard;

class Issue
{
    const STATE_ALL = 'all';
    const STATE_OPEN = 'open';
    const STATE_CLOSED = 'closed';

    /**
     * @var int id of the issue
     */
    private int $id;

    /**
     * @var string Title of the issue
     */
    private string $title;

    /**
     * @var array Issue labels
     */
    private array $labels;

    /**
     * @var string State of the issue. "open"|"closed"
     */
    private string $state;

    /**
     * @var array|null Issue's assignee
     */
    private ?array $assignee;

    /**
     * @var array|null Issue's assignees list
     */
    private ?array $assignees;

    /**
     * @var \DateTime Issue creation date
     */
    private \DateTime $createdAt;

    /**
     * Deny creating issues manually.
     */
    private function __construct()
    {
    }

    /**
     * Gets the id of the issue
     *
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Sets the id of the issue
     *
     * @param int $id
     */
    protected function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * Get title of the issue
     *
     * @return string
     */
    public function getTitle(): string
    {
        return $this->title;
    }

    /**
     * Set title of the issue
     *
     * @param string $title
     */
    protected function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * Get labels of the issue
     *
     * @return array
     */
    public function getLabels(): array
    {
        return $this->labels;
    }

    /**
     * Set labels of the issue
     *
     * @param array $labels
     */
    protected function setLabels(array $labels): void
    {
        $this->labels = $labels;
    }

    /**
     * Checks if the issue has labels
     *
     * @return bool
     */
    public function hasLabels(): bool {
        return !empty($this->labels);
    }

    /**
     * Get state of the issue
     *
     * @return string
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * Set state of the issue
     *
     * @param string $state
     */
    protected function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * Get assignee of the issue
     *
     * @return array|null
     */
    public function getAssignee(): ?array
    {
        return $this->assignee;
    }

    /**
     * Set assignee of the issue
     *
     * @param array|null $assignee
     */
    protected function setAssignee(?array $assignee): void
    {
        $this->assignee = $assignee;
    }

    /**
     * Get assignees of the issue
     *
     * @return array|null
     */
    public function getAssignees(): ?array
    {
        return $this->assignees;
    }

    /**
     * Set assignees of the issue
     *
     * @param array|null $assignees
     */
    protected function setAssignees(?array $assignees): void
    {
        $this->assignees = $assignees;
    }

    /**
     * Get creation date of the issue
     *
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime
    {
        return $this->createdAt;
    }

    /**
     * Set creation date of the issue
     *
     * @param \DateTime $createdAt
     */
    protected function setCreatedAt(\DateTime $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Checks if the issue is assigned to somebody
     *
     * @return bool
     */
    public function isAssigned(): bool {
        return (!empty($this->assignee) || !empty($this->assignees));
    }

    /**
     * Creates an instance from Github Response array
     *
     * @param array $githubResponseIssue
     * @return Issue
     * @throws \Exception
     */
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