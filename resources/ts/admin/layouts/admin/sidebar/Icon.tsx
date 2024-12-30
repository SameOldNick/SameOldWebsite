import React from 'react';
import { IconContext } from 'react-icons';

const Icon: React.FC<React.PropsWithChildren> = ({ children }) => {
    if (children === undefined)
        return <></>;

    return (
        <IconContext.Provider value={{ className: "fa-fw" }}>
            {children}
        </IconContext.Provider>
    );
}

export default Icon;
