import React from 'react';

import DropdownMenuItem from '@admin/utils/menus/items/DropdownMenuItem';
import ItemWrapper from './ItemWrapper';
import { DropdownMenu, DropdownToggle, UncontrolledDropdown } from 'reactstrap';
import NavIcon from './NavIcon';

interface IProps {
    item: DropdownMenuItem;
}

const DropdownWrapper: React.FC<IProps> = ({ item }) => {
    return (
        <>
            <UncontrolledDropdown nav inNavbar>
                <DropdownToggle nav caret>
                    {item.options?.icon && <NavIcon icon={item.options.icon} />}

                    {item.text}
                </DropdownToggle>
                <DropdownMenu right>
                    {item.items.map((menuItem, index) => <ItemWrapper key={index} inDropdown item={menuItem} />)}
                </DropdownMenu>
            </UncontrolledDropdown>
        </>
    );

};

export default DropdownWrapper;
