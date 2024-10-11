import React from 'react';

import LinkMenuItem from '@admin/utils/menus/items/LinkMenuItem';
import Item from '../Item';

interface IProps {
    item: LinkMenuItem;
}

const LinkItemWrapper: React.FC<IProps> = ({ item }) => {
    const icon = React.useMemo(() => item.options?.icon ? React.createElement(item.options?.icon) : undefined, [item.options]);

    return (
        <>
            <Item href={item.href} icon={icon}>{item.text}</Item>

        </>
    );

};

export default LinkItemWrapper;
