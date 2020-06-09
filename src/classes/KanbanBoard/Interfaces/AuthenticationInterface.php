<?php declare(strict_types=1);

namespace App\KanbanBoard\Interfaces;


interface AuthenticationInterface
{
    /**
     * Login method implementation
     *
     * @return string access_token
     */
    public function login(): string;

    /**
     * Logout method implementation
     *
     * @return void
     */
    public function logout(): void;
}