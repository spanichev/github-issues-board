<?php declare(strict_types=1);

namespace App\KanbanBoard\Interfaces;

use Tightenco\Collect\Support\Collection;

interface GithubServiceInterface
{
    /**
     * Implementation for milestones method
     * @param string $repository
     * @return Collection
     */
    public function milestones(string $repository): Collection;

    /**
     * Implementation for issues method
     * @param string $repository
     * @return Collection
     */
    public function issues(string $repository): Collection;
}