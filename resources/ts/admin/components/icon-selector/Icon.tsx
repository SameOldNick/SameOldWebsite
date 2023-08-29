import React from 'react';

import { IIconType } from './IconSelector';

import { createIconFromSvgJson } from './utils';

interface IIconProps {
    icon: IIconType;
    size: number;
}

const Icon: React.FC<IIconProps> = ({ icon, size }) => {
    return React.useMemo(() => {
        const { svg: { tag, props, children } } = icon;

        return createIconFromSvgJson({
            tag,
            props: { width: size, height: size, ...props },
            children
        });
    }, [icon, size]);
}

export default Icon;
