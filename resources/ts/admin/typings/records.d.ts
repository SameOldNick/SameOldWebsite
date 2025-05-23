declare global {

    interface IUser {
        id?: number;
        name: string;
        email: string;
        state: IState | null;
        country: ICountry;
        roles: IRole[];
        avatar_url: string;
        oauth_providers: OAuthProvider[];
        created_at: string;
        updated_at: string | null;
        deleted_at: string | null;

        [key: string | number]: any;
    }

    interface IRole {
        role: TRole;
    }

    type TRole =
        "change_avatar" |
        "change_contact_settings" |
        "edit_profile" |
        "manage_backups" |
        "manage_images" |
        "manage_comments" |
        "manage_projects" |
        "manage_users" |
        "receive_contact_messages" |
        "view_contact_messages" |
        "write_posts";

    interface OAuthProvider {
        provider_name: string;
        avatar_url: string;
        created_at: string;
        updated_at: string;
        expires_at: string;
    }

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

    interface IFileMeta {
        size: number;
        last_modified: string;
        mime_type: string;
    }

    interface IFile {
        id: string;
        name: string;
        meta: IFileMeta;
        created_at: string;
        updated_at: string | null;
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
        person: IPerson;
        created_at: string;
        updated_at: string;
        deleted_at: string | null
    }

    interface IPerson {
        name: string | null;
        email: string | null;
        user_id: number | null;
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
        extra: {
            url: string;
        };
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
        extra: {
            url: string;
        };
    }

    interface IComment {
        id?: number;
        parent_id: number | null;
        article_id: number;
        post: IPost;
        article: IArticle;
        title: string | null;
        comment: string;
        commenter: Record<'display_name' | 'name' | 'email', string>;
        status: TCommentStatuses;
        extra: {
            url: string;
        };
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
        status: string;
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

    interface IBackupSetting {
        key: string;
        value: string;
    }

    interface IBackupDestination {
        id: number;
        enable: boolean;
        name: string;
        type: 'ftp' | 'sftp';
        host: string;
        port: number;
        auth_type: 'password' | 'key';
        username: string;
    }

    interface IBlacklistEntry {
        id: number;
        input: 'name' | 'email';
        type: 'regex' | 'static';
        value: string;
        created_at: string;
        updated_at: string;
    }
}

export { };
