import React from 'react';

import PageVisibility from 'react-page-visibility';

interface IPageVisibilityWrapperProps {
    children: React.ReactNode | ((visible: boolean) => React.ReactNode);
}

const PageVisibilityWrapper: React.FC<IPageVisibilityWrapperProps> = ({ children }) => {
    const [isVisible, setIsVisible] = React.useState(false);

    const handleVisibilityChange = (isVisible: boolean) => {
        setIsVisible(isVisible);
    }

    return (
        <>
            <PageVisibility onChange={handleVisibilityChange} />

            {typeof children === 'function' ? children(isVisible) : children}
        </>
    );
}

export default PageVisibilityWrapper;
