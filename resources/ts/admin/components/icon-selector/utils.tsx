import React from 'react';

import { IIconType, ISvg } from './IconSelector';

import bladeIcons from './icons/blade-icons.json';

const getAllIcons = () => Object.entries(bladeIcons).flatMap<IIconType>(
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

const lookupIcon = (icon: string): IIconType | undefined => {
    const prefix = icon.split('-')[0];

    const name = icon.substring(icon.indexOf('-') + 1);

    for (const [family, value] of Object.entries(bladeIcons)) {
        if (value.prefix === prefix && name in value.icons) {
            const svg = (value.icons as Record<string, ISvg>)[name];

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

export { getAllIcons, createIconFromSvgJson, lookupIcon };
