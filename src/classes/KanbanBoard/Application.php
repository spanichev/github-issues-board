<?php
namespace App\KanbanBoard;

use App\KanbanBoard\Interfaces\GithubServiceInterface;
use App\Utilities;
use Tightenco\Collect\Support\Collection;

class Application {

    /**
     * @var GithubServiceInterface $githubService
     */
    protected GithubServiceInterface $githubService;

    /**
     * @var array|string[] $repositories
     */
    protected array $repositories;

    /**
     * @var array|string[] $pausedLabels
     */
    protected array $pausedLabels;

    /**
     * Application constructor.
     *
     * @param GithubServiceInterface $githubService
     * @throws \App\Exceptions\EnvVariableNotFoundException
     */
	public function __construct(GithubServiceInterface $githubService)
	{
        $this->githubService = $githubService;
		$this->repositories = explode('|', Utilities::env('GH_REPOSITORIES'));
		$this->pausedLabels = explode('|', Utilities::env('GH_PAUSED_LABELS'));;
	}

    /**
     * Gets milestones for given repositories with issues. Sorts milestones by name.
     *
     * @return Collection
     */
    public function getMilestones(): Collection {
        $milestones = new Collection();
        foreach ($this->repositories as $repository) {
            $milestones = $milestones->union($this->githubService->milestones($repository, true));
        }
        $milestones->sortBy('name');
        return $milestones;
    }

    /**
     * Returns data required by mustache file
     *
     * @return array
     */
	public function board(): array
	{
	    $result = [];
	    $milestones = $this->getMilestones();

        foreach ( $milestones as $milestone ) {
            $result[] = [
                'milestone' => $milestone->getName(),
                'url' => $milestone->getHtmlUrl(),
                'progress' => $milestone->getProgress(),
                'queued' => Utilities::mapToView($milestone->queuedIssues()),
                'active' => Utilities::mapToView($milestone->activeIssues()),
                'completed' => Utilities::mapToView($milestone->closedIssues())
            ];
        }

        return $result;
	}
}
