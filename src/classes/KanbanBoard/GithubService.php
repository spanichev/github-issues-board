<?php declare(strict_types=1);

namespace App\KanbanBoard;

use App\KanbanBoard\Interfaces\GithubServiceInterface;
use App\Utilities;
use Github\Client;
use Github\Api\Issue\Milestones;

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
     * @return array
     */
    public function milestones(string $repository): array {
        return $this->milestoneApi->all($this->account, $repository);
    }

    /**
     * Gets issues for user account and repository
     *
     * @param string $repository Name of the github repository
     * @param int|null $milestoneId MilestoneId, if we need to filter issues by milestones
     * @param string $state If we need to filter issues by state.
     * @return array<\App\KanbanBoard\Issue> Array if Issue objects
     */
    public function issues(string $repository, int $milestoneId = null, string $state = Issue::STATE_ALL): array {
        $issueParameters = ['state' => $state];
        if ( !empty($milestoneId) ) {
            $issueParameters['milestone'] = $milestoneId;
        }

        $issues = $this->client->api('issue')->all($this->account, $repository, $issueParameters);

        $processedIssues = [];
        foreach ( $issues as $issue ) {
            $processedIssue = Issue::createFromGithubResponse($issue);
            $processedIssues[] = $processedIssue;
        }

        return $processedIssues;
    }

    /**
     * Gets issues with open state
     *
     * @param string $repository Name of the github repository
     * @param int|null $milestoneId MilestoneId, if we need to filter issues by milestones
     * @return array<\App\KanbanBoard\Issue> Array if Issue objects
     */
    public function openIssues(string $repository, int $milestoneId = null): array {
        return $this->issues($repository, $milestoneId, Issue::STATE_OPEN);
    }

    /**
     * Gets issues with closed state
     *
     * @param string $repository Name of the github repository
     * @param int|null $milestoneId MilestoneId, if we need to filter issues by milestones
     * @return array<\App\KanbanBoard\Issue> Array if Issue objects
     */
    public function closedIssues(string $repository, int $milestoneId = null): array {
        return $this->issues($repository, $milestoneId, Issue::STATE_CLOSED);
    }

}