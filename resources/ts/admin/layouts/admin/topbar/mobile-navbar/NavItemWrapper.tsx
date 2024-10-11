import React from 'react';
import { NavItem, NavLink } from 'reactstrap';

import LinkMenuItem from '@admin/utils/menus/items/LinkMenuItem';
import NavIcon from './NavIcon';


interface NavItemWrapperProps {
    item: LinkMenuItem;
}

const NavItemWrapper: React.FC<NavItemWrapperProps> = ({ item }) => {
    return (
        <>
            <NavItem>
                <NavLink href={item.href}>
                    {item.options?.icon && <NavIcon icon={item.options.icon} />}

                    {item.text}
                </NavLink>
            </NavItem>

        </>
    );

};

export default NavItemWrapper;
