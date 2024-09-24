import Notification from "./Notification";

export type TActivityNotificationType = 'ce659a33-08dd-4c9c-a421-7bb54393b76d';

export enum ActivityEvent {
    UserRegistered = 'user-registered',
    CommentCreated = 'comment-created',
    ArticleCreated = 'article-created',
    ArticlePublished = 'article-published',
    ArticleScheduled = 'article-scheduled',
    ArticleUnpublished = 'article-unpublished',
    ArticleDeleted = 'article-deleted',
}

export interface IActivityNotificationData<TContext extends object = object> {
    dateTime: string;
    event: ActivityEvent;
    message: string;
    context: TContext;
}

export default class ActivityNotification extends Notification<TActivityNotificationType, IActivityNotificationData> {
    /**
     * Gets security event
     *
     * @readonly
     * @memberof ActivityNotification
     */
    public get event() {
        return this.getData().event;
    }
}
