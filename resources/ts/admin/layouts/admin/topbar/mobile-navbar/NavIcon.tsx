import React from 'react';

import { IconType } from 'react-icons';

interface NavIconProps {
    icon: IconType;
}

const NavIcon: React.FC<NavIconProps> = ({ icon: iconType }) => {
    const icon = React.useMemo(() => React.createElement(iconType), [iconType]);

    return (
        <>
            {icon && (
                <span className='me-1'>
                    {icon}
                </span>
            )}
        </>
    );
};

export default NavIcon;
