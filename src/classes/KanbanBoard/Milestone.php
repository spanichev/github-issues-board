<?php declare(strict_types=1);

namespace App\KanbanBoard;

use Tightenco\Collect\Support\Collection;

class Milestone
{
    /**
     * @var int $id id of the milestone
     */
    private int $id;

    /**
     * @var int $number
     */
    private int $number;

    /**
     * @var string $name
     */
    private string $name;

    /**
     * @var string $htmlUrl
     */
    private string $htmlUrl;

    /**
     * @var string $repository
     */
    private string $repository;

    /**
     * @var Collection $issues
     */
    private Collection $issues;

    /**
     * Deny creating empty objects.
     */
    private function __construct()
    {
    }

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
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
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
     * @return string
     */
    public function getRepository(): string
    {
        return $this->repository;
    }

    /**
     * @param string $repository
     */
    public function setRepository(string $repository): void
    {
        $this->repository = $repository;
    }

    /**
     * @return Collection
     */
    public function getIssues(): Collection
    {
        return $this->issues;
    }

    /**
     * @param Collection $issues
     */
    public function setIssues(Collection $issues): void
    {
        $this->issues = $issues;
    }

    /**
     * Gets closed issues
     *
     * @return Collection
     */
    public function closedIssues(): Collection {
        $closed = $this->issues->filter(function ($value) {
            return $value->getState() == Issue::STATE_CLOSED;
        });
        return $closed->collect();
    }

    /**
     * Gets open issues
     *
     * @return Collection
     */
    public function openIssues(): Collection {
        $closed = $this->issues->filter(function ($value) {
            return $value->getState() == Issue::STATE_OPEN;
        });
        return $closed->collect();
    }

    /**
     * Gets queued issues
     *
     * @return Collection
     */
    public function queuedIssues(): Collection {
        $queued = $this->issues->filter(function ($value) {
            return ($value->getState() == Issue::STATE_OPEN && !$value->isAssigned());
        });
        return $queued->collect();
    }

    /**
     * Gets active issues
     *
     * @return Collection
     */
    public function activeIssues(): Collection {
        $active = $this->issues->filter(function ($value) {
            return ($value->getState() == Issue::STATE_OPEN && $value->isAssigned());
        });
        return $active->collect();
    }

    /**
     * Gets progress for the milestone
     *
     * @return int
     */
    public function getProgress(): int {
        $completed = $this->closedIssues()->count();
        $remaining = $this->openIssues()->count();
        if ( $remaining > $completed ) {
            return (int)round($completed / $remaining * 100);
        }
        return 100;
    }

    /**
     * Creates instance from Github Response
     *
     * @param array $githubMilestoneItem
     * @param string $repository
     * @return Milestone
     */
    public static function createFromGithubResponse(array $githubMilestoneItem, string $repository): Milestone {
        $milestone = new Milestone();
        $milestone->setId($githubMilestoneItem['id']);
        $milestone->setNumber($githubMilestoneItem['number']);
        $milestone->setName($githubMilestoneItem['title']);
        $milestone->setHtmlUrl($githubMilestoneItem['html_url']);
        $milestone->setRepository($repository);

        return $milestone;
    }
}