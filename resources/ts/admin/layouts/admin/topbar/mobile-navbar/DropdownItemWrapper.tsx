import React from 'react';

import LinkMenuItem from '@admin/utils/menus/items/LinkMenuItem';
import { DropdownItem, DropdownItemProps } from 'reactstrap';
import NavIcon from './NavIcon';

interface DropdownItemWrapperProps extends Omit<DropdownItemProps, 'href' | 'children'> {
    item: LinkMenuItem;
}

const DropdownItemWrapper: React.FC<DropdownItemWrapperProps> = ({ item, ...props }) => {
    return (
        <>
            <DropdownItem href={item.href} {...props}>
                {item.options?.icon && <NavIcon icon={item.options.icon} />}

                {item.content}
            </DropdownItem>
        </>
    );

};

export default DropdownItemWrapper;
