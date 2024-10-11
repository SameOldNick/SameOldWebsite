import React from 'react';
import { Collapse, Nav, Navbar, NavbarProps } from 'reactstrap';

import MenuItem from '@admin/utils/menus/items/MenuItem';

import ItemWrapper from './ItemWrapper';

interface MobileNavbarProps extends NavbarProps {
    items: MenuItem[];
    isOpen: boolean;
}

const MobileNavbar: React.FC<MobileNavbarProps> = ({ items, isOpen, ...props }) => {
    return (
        <Navbar {...props}>
            <Collapse isOpen={isOpen} navbar>
                <Nav navbar vertical>
                    {items.map((item, index) => <ItemWrapper key={index} item={item} />)}
                </Nav>
            </Collapse>
        </Navbar>
    );

};

export default MobileNavbar;
