import React from 'react';
import { IconContext } from 'react-icons';

interface IconProps extends React.PropsWithChildren {

}

const Icon: React.FC<IconProps> = ({ children }) => {
    if (children === undefined)
        return <></>;

    return (
        <IconContext.Provider value={{ className: "fa-fw" }}>
            {children}
        </IconContext.Provider>
    );
}

export default Icon;
export { IconProps };
