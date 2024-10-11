import React from 'react';

import MenuItem from '@admin/utils/menus/items/MenuItem';

import ItemWrapper from './ItemWrapper';
import Sidebar, { SidebarProps } from '../../Sidebar';

interface IProps extends SidebarProps {
    items: MenuItem[];
}

const SidebarWrapper: React.FC<IProps> = ({ items, ...props }) => {
    return (
        <Sidebar {...props}>
            {items.map((item, index) => <ItemWrapper key={index} item={item} />)}
        </Sidebar>
    );

};

export default SidebarWrapper;
