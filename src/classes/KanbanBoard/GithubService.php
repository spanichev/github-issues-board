<?php declare(strict_types=1);

namespace App\KanbanBoard;

use App\KanbanBoard\Interfaces\GithubServiceInterface;
use App\Utilities;
use Github\Client;
use Github\Api\Issue\Milestones;
use Tightenco\Collect\Support\Collection;

class GithubService implements GithubServiceInterface
{
    /**
     * @var Client $client KnpLabs Github Client
     */
    private Client $client;

    /**
     * @var array|Milestones
     */
    private Milestones $milestoneApi;

    /**
     * @var string GitHub user's account
     */
    private string $account;

    /**
     * GithubClient constructor.
     *
     * @param string $accessToken Api access token
     * @throws \App\Exceptions\EnvVariableNotFoundException
     */
    public function __construct(string $accessToken) {
        $this->client = new Client();
        $this->client->authenticate($accessToken, Client::AUTH_HTTP_TOKEN);
        $this->milestoneApi = $this->client->api('issues')->milestones();
        $this->account = Utilities::env('GH_ACCOUNT');
    }

    /**
     * Gets milestones for account
     *
     * @param string $repository Name of github repository
     * @param bool $withIssues Should we fetch issues for the milestone
     * @return Collection<Milestone>
     */
    public function milestones(string $repository, bool $withIssues = false): Collection {
        $milestones = $this->milestoneApi->all($this->account, $repository);

        $processedMilestones = new Collection();
        foreach ( $milestones as $milestone ) {
            $processedMilestone = Milestone::createFromGithubResponse($milestone, $repository);
            if ( $withIssues === true ) {
                $processedMilestone->setIssues($this->issues($repository, $processedMilestone->getNumber()));
            }
            $processedMilestones->add($processedMilestone);
        }
        return $processedMilestones;
    }

    /**
     * Gets issues for user account and repository
     *
     * @param string $repository Name of the github repository
     * @param int|null $milestoneId Milestone number, if we need to filter issues by milestones
     * @param bool $ignorePullRequests Should we skip issues from pull-requests
     * @param string $state If we need to filter issues by state.
     * @return Collection<\App\KanbanBoard\Issue> Array if Issue objects
     */
    public function issues(string $repository, int $milestoneId = null, bool $ignorePullRequests = true, string $state = Issue::STATE_ALL): Collection {
        $issueParameters = ['state' => $state];
        if ( !empty($milestoneId) ) {
            $issueParameters['milestone'] = $milestoneId;
        }

        $issues = $this->client->api('issue')->all($this->account, $repository, $issueParameters);

        $processedIssues = new Collection();
        foreach ( $issues as $issue ) {
            if ( $ignorePullRequests && !empty($issue['pull_request']) ) continue;

            $processedIssue = Issue::createFromGithubResponse($issue);
            $processedIssues->add($processedIssue);
        }

        return $processedIssues;
    }
}