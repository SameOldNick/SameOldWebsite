import React from 'react';

import LinkMenuItem from '@admin/utils/menus/items/LinkMenuItem';
import DropdownItem from '../DropdownItem';

interface DropdownLinkItemWrapperProps {
    item: LinkMenuItem;
}

const DropdownLinkItemWrapper: React.FC<DropdownLinkItemWrapperProps> = ({ item }) => {
    const icon = React.useMemo(() => item.options?.icon ? React.createElement(item.options?.icon) : undefined, [item.options]);

    return (
        <>
            <DropdownItem href={item.href} icon={icon}>{item.text}</DropdownItem>
        </>
    );

};

export default DropdownLinkItemWrapper;
