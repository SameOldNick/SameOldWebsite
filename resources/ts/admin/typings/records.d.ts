declare global {

    interface IUser {
        id?: number;
        name: string;
        email: string;
        state: IState | null;
        country: ICountry;
        roles: IRole[];
        avatar_url: string;
        created_at: string;
        updated_at: string | null;
        deleted_at: string | null;

        [key: string | number]: any;
    }

    interface IRole {
        role: TRole;
    }

    type TRole = "change_avatar" | "change_contact_settings" | "edit_profile" | "manage_backups" | "manage_comments" | "manage_projects" | "manage_users" | "receive_contact_messages" | "view_contact_messages" | "write_posts";

    interface IState {
        code: string;
        state: string;
    }

    interface ICountry {
        code: string;
        code_alpha2: string;
        country: string;
        states: IState[];
    }

    interface IProduct {
        id: number;
        name: string;
        slug: string;
        description: string;
        summary_auto: boolean;
        summary: string;
        tags: Record<string | number, string>;
        faq_category: IFaqCategory | null;
        main_image: IImage | null;
        position: number | null;
        featured: boolean;
        deleted_at: string | null;
    }

    interface IFileMeta {
        size: number;
        last_modified: string;
        mime_type: string;
    }

    interface IFile {
        id: string;
        name: string;
        url: string | null;
        meta: IFileMeta;
        created_at: string;
        updated_at: string | null;
    }

    interface IProductImage {
        id: number;
        position: number | null;
        file: IFile;
    }

    interface IProductOffering {
        id: number;
        title: string;
        description: string | null;
        old_price: number | null;
        price: number;
        old_price_formatted: string | null;
        price_formatted: string;
        currency_code: string;
        created_at: string;
        updated_at: string | null;
        deleted_at: string | null;
    }

    interface IFaqCategory {
        id: number;
        category: string;
        parent: IFaqCategory | null;
    }

    interface IImage {

    }

    interface IProject {
        id?: number;
        project: string;
        description: string;
        url: string;
        tags: ITag[];
        created_at: string;
        updated_at: string;
        deleted_at: string | null;
    }

    interface ITag {
        id?: number;
        slug: string | null;
        tag: string;
    }

    interface IPage {
        page: string;
    }

    interface IPageMetaData {
        key: string;
        value: string;
    }

    type TSocialMediaLink = string;

    interface ISocialMediaLink {
        id?: number;
        link: string;
        created_at: string;
        updated_at: string;
    }

    interface ISkill {
        id?: number;
        icon: string;
        skill: string;
    }

    interface ITechnology {
        id?: number;
        icon: string;
        technology: string;
    }

    interface IPost {
        id: number;
        user_id: number;
        user: IUser | null;
        created_at: string;
        updated_at: string;
        deleted_at: string | null
    }

    interface IArticle {
        id: number;
        title: string;
        slug: string;
        main_image: IImage | null;
        revision?: IRevision;
        current_revision: IRevision | null;
        published_at: string | null;
        deleted_at: string | null;
        private_url?: string;
    }

    interface IRevision {
        uuid: string;
        content: string;
        summary: string;
        summary_auto: boolean;
        created_at: string;
        updated_at: string;
    }

    interface IImage {
        uuid: string;
        description: string;
        file: IFile;
    }

    interface IComment {
        id?: number;
        parent_id: number | null;
        article_id: number;
        post: IPost;
        article: IArticle;
        title: string | null;
        comment: string;
        commenter_info: Record<'display_name' | 'email', string>;
        status: TCommentStatuses;
    }

    type TCommentStatuses = 'approved' | 'denied' | 'flagged' | 'awaiting_verification' | 'awaiting_approval' | 'locked';

    interface IChartVisitors {
        [dateISO8601: string]: {
            newUsers: number;
            totalUsers: number;
        };
    }

    interface IChartLinks {
        [url: string]: number;
    }

    interface IChartBrowsers {
        [browser: string]: number;
    }

    interface IContactMessage {
        uuid: string;
        name: string;
        email: string;
        message: string;
        created_at: string;
        updated_at: string | null;
        confirmed_at: string | null;
        expires_at: string | null;
    }

    type TBackupStatuses = 'successful' | 'failed' | 'not-exists' | 'deleted';

    interface IBackup {
        uuid: string;
        status: TBackupStatuses;
        error_message?: string;
        created_at: string;
        updated_at: string | null;
        file: IFile | null;
    }
}

export { };
