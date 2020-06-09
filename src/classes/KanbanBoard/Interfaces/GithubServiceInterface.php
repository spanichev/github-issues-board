<?php declare(strict_types=1);

namespace App\KanbanBoard\Interfaces;

use Tightenco\Collect\Support\Collection;

interface GithubServiceInterface
{
    /**
     * Implementation for milestones method
     * @param string $repository
     * @param bool $withIssues Indicates if we should include issues there
     * @return Collection
     */
    public function milestones(string $repository, bool $withIssues = false): Collection;

    /**
     * Implementation for issues method
     * @param string $repository
     * @return Collection
     */
    public function issues(string $repository): Collection;
}