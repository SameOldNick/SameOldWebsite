import React from 'react';

interface IIconsFile {
    file: string;
    icons: IIconsFileContents;
}

interface IIconsFileContents {
    [group: string]: {
        prefix: string;
        icons: Record<string, ISvg>;
    }
}

interface ISvg {
    tag: string;
    props: Record<string, string | number>;
    children?: ISvg[];
}

interface IIconType {
    family: string;
    prefix: string;
    name: string;
    svg: ISvg;
}

const loadIconsFromFile = async (): Promise<IIconsFile> => {
    const icons = await import('./icons/blade-icons.json');

    return {
        file: './icons/blade-icons.json',
        icons: icons.default
    };
}

const getAllIcons = (file: IIconsFile) => Object.entries(file.icons).flatMap<IIconType>(
    ([family, { prefix, icons }]) =>
        Object.entries(icons).map<IIconType>(([key, value]) => ({
            family,
            prefix,
            name: key,
            svg: value
        })
    )
);

const createIconFromSvgJson = ({ tag, props, children }: ISvg, index: number = 0): React.ReactNode =>
    React.createElement(
        tag,
        { ...props, key: index },
        children?.map((child, i) => createIconFromSvgJson(child, i))
    );

const lookupIcon = (file: IIconsFile, icon: string): IIconType | undefined => { console.log(file);
    const prefix = icon.split('-')[0];

    const name = icon.substring(icon.indexOf('-') + 1);

    for (const [family, value] of Object.entries(file.icons)) { console.log(family, value);
        if (value.prefix === prefix && name in value.icons) {
            const svg = (value.icons)[name];

            return {
                family,
                prefix,
                name,
                svg
            };
        }
    }

    return undefined;
}

export { IIconsFile, ISvg, IIconType, loadIconsFromFile, getAllIcons, createIconFromSvgJson, lookupIcon };
