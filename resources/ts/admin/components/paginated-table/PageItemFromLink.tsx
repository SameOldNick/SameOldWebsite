import React from 'react';

import PageItem from './PageItem';

interface PageItemFromLinkProps extends Omit<React.HTMLProps<HTMLLIElement>, 'onClick'> {
    link: IPaginateResponseLink;
    onClick: (e: React.MouseEvent, link: string) => void;
    anchorProps?: React.HTMLProps<HTMLAnchorElement>;
}

/**
 * Page Item from IPaginateResponseLink component
 *
 * @exports
 * @type {React.FC<IPageItemFromLinkProps>}
 */
const PageItemFromLink: React.FC<PageItemFromLinkProps> = ({ link: { url, active, label }, onClick, ...props }) => {
    return (
        <PageItem link={url ?? undefined} active={active} onClick={onClick} {...props}>
            {label}
        </PageItem>
    );
};

export default PageItemFromLink;
