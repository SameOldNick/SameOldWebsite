import React from 'react';

import classNames from 'classnames';

interface PageItemProps extends Omit<React.HTMLProps<HTMLLIElement>, 'onClick'> {
    link?: string;
    onClick: (e: React.MouseEvent, link: string) => void;
    disabled?: boolean;
    active?: boolean;
    anchorProps?: React.HTMLProps<HTMLAnchorElement>;
}

/**
 * PageItem component
 *
 * @exports
 * @type {React.FC<React.PropsWithChildren<IPageItemProps>>}
 */
const PageItem: React.FC<PageItemProps> = ({ children, disabled, active, link, onClick, anchorProps, ...props }) => {
    return (
        <li className={classNames("page-item", { disabled: disabled ?? true, active: active ?? false })} aria-disabled={disabled ? true : undefined} {...props}>
            {
                disabled ?
                    <span className="page-link">
                        {children}
                    </span> :
                    <a className='page-link' href='#' onClick={(e) => onClick(e, link ?? '')} {...anchorProps}>
                        {children}
                    </a>
            }
        </li>
    );
};

export default PageItem;
