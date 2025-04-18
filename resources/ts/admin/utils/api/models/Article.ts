import { DateTime } from "luxon";
import S from "string";
import { generatePath } from "react-router-dom";

import Revision from "./Revision";
import Image from "./Image";

export type TArticleStatusRevision = 'revision';
export type TArticleStatusPublished = 'published';
export type TArticleStatusScheduled = 'scheduled';
export type TArticleStatusDeleted = 'deleted';

export type TArticleStatus = TArticleStatusRevision | TArticleStatusPublished | TArticleStatusScheduled | TArticleStatusDeleted;

export default class Article {
    public static readonly ARTICLE_STATUS_UNPUBLISHED = 'revision';
    public static readonly ARTICLE_STATUS_PUBLISHED = 'published';
    public static readonly ARTICLE_STATUS_SCHEDULED = 'scheduled';
    public static readonly ARTICLE_STATUS_DELETED = 'deleted';

    public readonly currentRevision: Revision | null;

    constructor(
        public readonly article: IArticle
    ) {
        this.currentRevision = article.current_revision !== null ? new Revision(article.current_revision) : null;
    }

    /**
     * Gets the URL to the article (on the main site)
     *
     * @readonly
     * @memberof Article
     */
    public get url() {
        return this.article.extra.url;
    }

    /**
     * Gets the main image
     *
     * @readonly
     * @memberof Article
     */
    public get mainImage() {
        return this.article.main_image ? new Image(this.article.main_image) : null;
    }

    /**
     * Gets when article was created
     *
     * @readonly
     * @memberof Article
     */
    public get createdAt() {
        return this.currentRevision ? this.currentRevision.createdAt : null;
    }

    /**
     * Gets when article is published (or null if draft)
     *
     * @readonly
     * @memberof Article
     */
    public get publishedAt() {
        return this.article.published_at !== null ? DateTime.fromISO(this.article.published_at) : null;
    }

    /**
     * Gets when article was deleted (or null if not deleted)
     *
     * @readonly
     * @memberof Article
     */
    public get deletedAt() {
        return this.article.deleted_at !== null ? DateTime.fromISO(this.article.deleted_at) : null;
    }

    /**
     * Gets article status
     *
     * @readonly
     * @type {TArticleStatus}
     * @memberof Article
     */
    public get status(): TArticleStatus {
        if (this.deletedAt === null) {
            if (this.publishedAt === null)
                return Article.ARTICLE_STATUS_UNPUBLISHED;
            else if (this.publishedAt.diffNow().toMillis() < 0)
                return Article.ARTICLE_STATUS_PUBLISHED;
            else
                return Article.ARTICLE_STATUS_SCHEDULED;
        } else {
            return Article.ARTICLE_STATUS_DELETED;
        }
    }

    /**
     * Checks if article slug is auto generated.
     *
     * @readonly
     * @memberof Article
     */
    public get isSlugAutoGenerated() {
        return Article.generateSlugFromTitle(this.article.title) === this.article.slug;
    }

    /**
     * Generates path to edit article.
     *
     * @param {string} [revisionUuid]
     * @returns Path to article
     * @memberof Article
     */
    public generatePath(revisionUuid?: string) {
        if (!this.article.id)
            throw new Error('Article is missing ID.');

        if (revisionUuid)
            return generatePath(`/admin/posts/edit/:article/revisions/:revision`, {
                article: this.article.id.toString(),
                revision: revisionUuid
            });
        else
            return generatePath(`/admin/posts/edit/:article`, {
                article: this.article.id.toString()
            });
    }

    /**
     * Generates slug for article title
     * @param title
     * @returns Slug (title in kebab-case)
     */
    public static generateSlugFromTitle(title: string) {
        return S(title).slugify().s;
    }
}
