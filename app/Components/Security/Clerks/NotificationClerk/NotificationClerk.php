<?php

namespace App\Components\Security\Clerks\NotificationClerk;

use App\Components\Security\Clerks\ClerkDriver;
use App\Components\Security\Issues\Issue;
use App\Models\Role;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Notification;

class NotificationClerk implements ClerkDriver
{
    public function __construct(
        private readonly array $config
    )
    {
    }

    /**
     * Checks if the issue is fresh/new.
     *
     * @param Issue $issue
     * @return boolean
     */
    public function isFresh(Issue $issue): bool {
        if (!$this->hasNotifiables())
            return false;

        if (!$this->isNotificationFresh($issue))
            return false;

        return true;
    }

    /**
     * File the issue
     *
     * @param Issue $issue
     * @return void
     */
    public function file(Issue $issue): void {

        Notification::send($this->getNotifiables(), $this->createNotificationFromIssue($issue));
    }

    /**
     * Gets the role that will receive the notification.
     *
     * @return string|null
     */
    protected function getRole() {
        return Arr::get($this->config, 'role');
    }

    /**
     * Check if notifiables exist.
     *
     * @return boolean
     */
    protected function hasNotifiables() {
        return count($this->getNotifiables()) > 0;
    }

    /**
     * Gets the notifiables (who will receive the notification)
     *
     * @return array
     */
    protected function getNotifiables() {
        if (is_null($this->getRole()))
            return [];

        return Role::firstWhere(['role' => $this->getRole()])->users;
    }

    /**
     * Checks if issue is fresh/new.
     *
     * @param Issue $issue
     * @return boolean
     */
    protected function isNotificationFresh(Issue $issue) {
        $notificationIssue = $this->createNotificationFromIssue($issue);

        $id = $notificationIssue->getId();
        $databaseType = $notificationIssue->databaseType();

        foreach ($this->getNotifiables() as $notifiable) {
            foreach ($notifiable->notifications as $notification) {
                if ($notification->type === $databaseType && Arr::get($notification->data, 'id') === $id) {
                    return false;
                }
            }
        }

        return true;
    }

    /**
     * Creates notification from an issue.
     *
     * @param Issue $issue
     * @return IssueNotification
     */
    protected function createNotificationFromIssue(Issue $issue) {
        return IssueNotification::createFromIssue($issue);
    }
}
