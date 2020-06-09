<?php
namespace App\KanbanBoard;

use App\KanbanBoard\Interfaces\GithubServiceInterface;
use App\Utilities;
use Michelf\Markdown;
use Tightenco\Collect\Support\Collection;

class Application {

    protected GithubServiceInterface $githubService;
    protected array $repositories;
    protected array $pausedLabels;

	public function __construct(GithubServiceInterface $githubService)
	{
        $this->githubService = $githubService;
		$this->repositories = explode('|', Utilities::env('GH_REPOSITORIES'));
		$this->pausedLabels = explode('|', Utilities::env('GH_PAUSED_LABELS'));;
	}

    public function getMilestones(): Collection {
        $milestones = new Collection();
        foreach ($this->repositories as $repository) {
            $milestones = $milestones->union($this->githubService->milestones($repository, true));
        }
        $milestones->sortBy('name');
        return $milestones;
    }

	public function board()
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

        var_dump($result);

        return $result;
        /*
		foreach ($ms as $name => $data)
		{
			$issues = $this->issues($data['repository'], $data['number']);
			$percent = self::_percent($data['closed_issues'], $data['open_issues']);
			if($percent)
			{
				$milestones[] = array(
					'milestone' => $name,
					'url' => $data['html_url'],
					'progress' => $percent,
					'queued' => $issues['queued'],
					'active' => $issues['active'],
					'completed' => $issues['completed']
				);
			}
		}
		return $milestones;
        */
	}

	private function issues($repository, $milestone_id)
	{
		$i = $this->github->issues($repository, $milestone_id);
		foreach ($i as $ii)
		{
			if (isset($ii['pull_request']))
				continue;
			$issues[$ii['state'] === 'closed' ? 'completed' : (($ii['assignee']) ? 'active' : 'queued')][] = array(
				'id' => $ii['id'], 'number' => $ii['number'],
				'title'            	=> $ii['title'],
				'body'             	=> Markdown::defaultTransform($ii['body']),
     'url' => $ii['html_url'],
				'assignee'         	=> (is_array($ii) && array_key_exists('assignee', $ii) && !empty($ii['assignee'])) ? $ii['assignee']['avatar_url'].'?s=16' : NULL,
				'paused'			=> self::labels_match($ii, $this->paused_labels),
				'progress'			=> self::_percent(
											substr_count(strtolower($ii['body']), '[x]'),
											substr_count(strtolower($ii['body']), '[ ]')),
				'closed'			=> $ii['closed_at']
			);
		}
		usort($issues['active'], function ($a, $b) {
			return count($a['paused']) - count($b['paused']) === 0 ? strcmp($a['title'], $b['title']) : count($a['paused']) - count($b['paused']);
		});
		return $issues;
	}

	private static function _state($issue)
	{
		if ($issue['state'] === 'closed')
			return 'completed';
		else if (Utilities::hasValue($issue, 'assignee') && count($issue['assignee']) > 0)
			return 'active';
		else
			return 'queued';
	}

    public function getPausedLabels(): array {
	    return $this->pausedLabels;
    }

	private static function _percent($complete, $remaining)
	{
		$total = $complete + $remaining;
		if($total > 0)
		{
			$percent = ($complete OR $remaining) ? round($complete / $total * 100) : 0;
			return array(
				'total' => $total,
				'complete' => $complete,
				'remaining' => $remaining,
				'percent' => $percent
			);
		}
		return array();
	}
}
