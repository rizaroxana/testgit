<?php

namespace App\Repositories;

use Illuminate\Database\Connection;
use Illuminate\Database\DatabaseManager;
use Illuminate\Http\Request;

class RepositoryBase
{
    /**
     * @var DatabaseManager
     */
    private $databaseManager;

    /**
     * CategoryRepository constructor.
     */
    public function __construct(DatabaseManager $databaseManager)
    {
        $this->databaseManager = $databaseManager;
    }

    /**
     * @return Connection
     */
    protected function connection()
    {
        return $this->databaseManager->connection('mysql');
    }

    /**
     * @param callable $callback
     * @return mixed
     */
    public function transaction(callable $callback)
    {
        return $this->connection()->transaction($callback);
    }

    /**
     * @param Request $request
     * @return int|null
     */
    public function getAuthenticatedUserId(Request $request)
    {
        return $request->user()->id ?? null;
    }
}