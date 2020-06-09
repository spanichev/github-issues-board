<?php


namespace App\KanbanBoard\Interfaces;


interface ArraybleInterface
{
    /**
     * All implementators should be convertable to Array
     *
     * @return array
     */
    public function toArray(): array;
}