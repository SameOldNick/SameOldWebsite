import classNames from 'classnames';
import React from 'react';

interface IProps extends React.PropsWithChildren {
    title?: string;
}

type HeadingTitleProps = React.PropsWithChildren<React.ComponentProps<'h1'>>;

const HeadingTitle: React.FC<HeadingTitleProps> = ({ children, className, ...props }) => (
    <h1 className={classNames("h3 mb-0 text-gray-800", className)} {...props}>
        {children}
    </h1>
);

const Heading: React.FC<IProps> = ({ title, children }) => {
    return (
        <>
            <div className="d-sm-flex align-items-center justify-content-between mb-4">
                {title && <HeadingTitle>{title}</HeadingTitle>}

                {children}
            </div>
        </>
    );
}

export { HeadingTitle };
export default Heading;
