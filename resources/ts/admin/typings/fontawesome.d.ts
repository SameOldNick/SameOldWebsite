declare module "@fortawesome/fontawesome-free" {
    export type TIconFamilies = Record<string, IIconFamily>;

    export interface IIconFamily {
        changes: string[];
        ligatures: any[];
        search: {
            terms: string[];
        };
        unicode: string;
        label: string;
        voted: boolean;
        svgs: Record<string, Record<string, {
            lastModified: number;
            raw: string;
            viewBox: number[];
            width: number;
            height: number;
            path: string;
        }>>;
        familyStylesByLicense: Record<'free' | 'pro', Array<object>>;
    }
}
