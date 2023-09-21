declare global {

    interface IUser {
        id?: number;
        name: string;
        email: string;
        state: IState | null;
        country: ICountry;
        roles: IRole[];

        [key: string | number]: any;
    }

    interface IRole {
        role: string;
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

    interface IArticle {
        id?: number;
        title: string;
        slug: string;
        main_image: IArticleImage | null;
        revision?: IRevision;
        current_revision: IRevision | null;
        published_at: string | null;
        deleted_at: string | null;
        private_url?: string;
    }

    interface IRevision {
        uuid?: string;
        content: string;
        summary: string;
        summary_auto: boolean;
        created_at: string;
        updated_at: string;
    }

    interface IArticleImage {
        id?: number;
        description: string;
        file: IFile;
    }
}

export { };
