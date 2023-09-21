import React from 'react';

interface IProps extends React.PropsWithChildren {
    title?: string;
}

const Heading: React.FC<IProps> = ({ title, children }) => {
    const HeadingTitle: React.FC<React.PropsWithChildren> = ({ children }) => (
        <h1 className="h3 mb-0 text-gray-800">{children}</h1>
    );

    return (
        <>
            <div className="d-sm-flex align-items-center justify-content-between mb-4">
                {title && <HeadingTitle>{title}</HeadingTitle>}

                {children}
            </div>
        </>
    );
}

export default Heading;
