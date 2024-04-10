<?php

namespace App\Components\Security\Clerks\EloquentClerk;

use App\Components\Security\Clerks\ClerkDriver;
use App\Components\Security\Clerks\EloquentClerk\Issue as IssueModel;
use App\Components\Security\Issues\Issue;

final class EloquentClerk implements ClerkDriver
{
    public function __construct(
        protected readonly array $config
    ) {
    }

    /**
     * Checks if the issue is fresh/new.
     *
     * @param Issue $issue
     * @return bool
     */
    public function isFresh(Issue $issue): bool
    {
        $hash = $this->getHash($issue);

        return IssueModel::where('hash', $hash)->doesntExist();
    }

    /**
     * File the issue
     *
     * @param Issue $issue
     * @return void
     */
    public function file(Issue $issue): void
    {
        $model = $this->mapIssueToModel($issue);

        $model->save();
    }

    protected function getHash(Issue $issue)
    {
        return hash('sha256', $issue->getFullIdentifier());
    }

    protected function mapIssueToModel(Issue $issue): IssueModel
    {
        $model = new IssueModel();

        $model->hash = $this->getHash($issue);
        $model->data = $issue->toArray();

        return $model;
    }
}
