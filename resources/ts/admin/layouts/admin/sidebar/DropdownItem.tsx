import React from 'react';
import { NavLink as ReactRouterNavLink, useLocation } from "react-router-dom";

import classNames from 'classnames';

import { DropdownContext } from './Dropdown';
import Icon from './Icon';

interface DropdownItemProps extends React.PropsWithChildren {
    href: string;
    icon?: React.ReactNode;
    exact?: boolean;
}

const DropdownItem: React.FC<DropdownItemProps> = ({ href, icon, exact, children }) => {
    const { setHasActive, inDropdown } = React.useContext(DropdownContext);

    const location = useLocation();

    if (!inDropdown) {
        logger.error('DropdownItem component must be inside Dropdown component.');
        return;
    }

    const locationMatches = React.useCallback((location: ReturnType<typeof useLocation>, href: string, exact: boolean = false) => {
        return exact ? location.pathname === href : location.pathname.startsWith(href) && location.pathname.charAt(href.length) === '/';
    }, []);

    const isActive = React.useMemo(() =>
        locationMatches(location, href, exact !== undefined ? exact : true),
        [locationMatches, location, href, exact]);

    React.useEffect(() => {
        if (isActive)
            setHasActive(true);
    }, [isActive]);

    return (
        <>
            {/* Using a function for classNames causes active class to not be automatically applied. */}
            <ReactRouterNavLink end={false} to={href} className={() => classNames('my-1 collapse-item', { active: isActive })}>
                <Icon>
                    {icon}
                </Icon>

                <span>{children}</span>
            </ReactRouterNavLink>
        </>
    );
}

export default DropdownItem;
export { DropdownItemProps };
