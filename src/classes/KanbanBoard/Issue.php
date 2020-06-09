<?php declare(strict_types=1);

namespace App\KanbanBoard;

use App\KanbanBoard\Interfaces\ArraybleInterface;
use Michelf\Markdown;

class Issue implements ArraybleInterface
{
    const STATE_ALL = 'all';
    const STATE_OPEN = 'open';
    const STATE_CLOSED = 'closed';

    /**
     * @var int id of the issue
     */
    private int $id;

    /**
     * @var int $number Number of the issue
     */
    private int $number;

    /**
     * @var string Title of the issue
     */
    private string $title;

    /**
     * @var string Body of the issue
     */
    private string $body;

    private string $htmlUrl;

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
     * @var \DateTime|null Issue close date
     */
    private ?\DateTime $closedAt;

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
    public function setId(int $id): void
    {
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getNumber(): int
    {
        return $this->number;
    }

    /**
     * @param int $number
     */
    public function setNumber(int $number): void
    {
        $this->number = $number;
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
    public function setTitle(string $title): void
    {
        $this->title = $title;
    }

    /**
     * @return string
     */
    public function getBody(): string
    {
        return $this->body;
    }

    /**
     * @param string $body
     */
    public function setBody(string $body): void
    {
        $this->body = Markdown::defaultTransform($body);
    }

    /**
     * @return string
     */
    public function getHtmlUrl(): string
    {
        return $this->htmlUrl;
    }

    /**
     * @param string $htmlUrl
     */
    public function setHtmlUrl(string $htmlUrl): void
    {
        $this->htmlUrl = $htmlUrl;
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
    public function setLabels(array $labels): void
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
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * Get assignee of the issue
     *
     * @return string|null
     */
    public function getAssignee(): ?string
    {
        if ( !empty($this->assignee) ) return $this->assignee['avatar_url'].'?s=16';

        return null;
    }

    /**
     * Set assignee of the issue
     *
     * @param array|null $assignee
     */
    public function setAssignee(?array $assignee): void
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
    public function setAssignees(?array $assignees): void
    {
        $this->assignees = $assignees;
    }

    /**
     * Get close date of the issue
     *
     * @return \DateTime
     */
    public function getClosedAt(): ?\DateTime
    {
        return $this->closedAt;
    }

    /**
     * Set close date of the issue
     *
     * @param \DateTime $createdAt
     */
    public function setClosedAt(?\DateTime $closedAt): void
    {
        $this->closedAt = $closedAt;
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
     * Converts the entity into mustache adapted array
     *
     * @return array
     */
    public function toArray(): array {
        return [
            'title' => $this->getTitle(),
            'id' => $this->getId(),
            'number' => $this->getNumber(),
            'body' => $this->getBody(),
            'labels' => $this->getLabels(),
            'paused' => [],
            'assignee' => $this->getAssignee(),
            'closed' => $this->getClosedAt() ? $this->getClosedAt()->format("Y-m-d H:i:s") :  null
        ];
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
        $issue->setNumber($githubResponseIssue['number']);
        $issue->setTitle($githubResponseIssue['title']);
        $issue->setLabels($githubResponseIssue['labels']);
        $issue->setState($githubResponseIssue['state']);
        $issue->setAssignee($githubResponseIssue['assignee']);
        $issue->setAssignees($githubResponseIssue['assignees']);
        $issue->setBody($githubResponseIssue['body']);
        $closedAt = (!empty($githubResponseIssue['closed_at'])) ? new \DateTime($githubResponseIssue['closed_at']) : null;
        $issue->setClosedAt($closedAt);
        return $issue;
    }
}