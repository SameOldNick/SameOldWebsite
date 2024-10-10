import React from 'react';
import { NavItem, NavLink } from 'reactstrap';
import { NavLink as ReactRouterNavLink } from "react-router-dom";

import Icon from './Icon';

interface ItemProps extends React.PropsWithChildren {
    href: string;
    icon?: React.ReactNode;
    exact?: boolean;
}

const Item: React.FC<ItemProps> = ({ href, icon, exact, children }) => {
    return (
        <NavItem>
            <NavLink tag={ReactRouterNavLink} to={href} end={exact}>
                <Icon>
                    {icon}
                </Icon>

                <span style={{ verticalAlign: 'middle' }}>
                    {children}
                </span>
            </NavLink>
        </NavItem>
    );
}

export default Item;
export { ItemProps };
