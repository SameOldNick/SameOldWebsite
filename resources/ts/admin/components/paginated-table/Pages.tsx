import React from 'react';

import PageItem from './PageItem';
import PageItemFromLink from './PageItemFromLink';
import PaginateResponse from '@admin/utils/api/models/PaginateResponse';

interface PagesProps {
    response: PaginateResponse;
    onPageSelect: (link: string) => void;
}


const Pages: React.FC<PagesProps> = ({ response, onPageSelect }) => {
    const handlePageItemClick = React.useCallback((e: React.MouseEvent, link: string) => {
        e.preventDefault();

        onPageSelect(link);
    }, [onPageSelect]);

    return (
        <nav className='d-flex justify-items-center justify-content-between'>
            <div className="d-flex justify-content-between flex-fill d-sm-none">
                <ul className="pagination">
                    {/* Previous Page Link */}
                    <PageItem link={response.previousPageUrl ?? '#'} disabled={response.onFirstPage} onClick={handlePageItemClick} anchorProps={{ rel: 'prev' }}>
                        &laquo; Previous
                    </PageItem>

                    {/* Next Page Link */}
                    <PageItem link={response.nextPageUrl ?? '#'} disabled={!response.hasMorePages} onClick={handlePageItemClick} anchorProps={{ rel: 'next' }}>
                        Next &raquo;
                    </PageItem>
                </ul>
            </div>

            <div className="d-none flex-sm-fill d-sm-flex align-items-sm-center justify-content-sm-between">
                <div>
                    <p className="text-muted">
                        Showing
                        {' '}
                        <span className="fw-semibold">{response.firstItem}</span>
                        {' '}
                        to
                        {' '}
                        <span className="fw-semibold">{response.lastItem}</span>
                        {' '}
                        of
                        {' '}
                        <span className="fw-semibold">{response.total}</span>
                        {' '}
                        results
                    </p>
                </div>

                <div>
                    <ul className="pagination">
                        {/* Previous Page Link */}
                        <PageItem link={response.previousPageUrl ?? '#'} disabled={response.onFirstPage} onClick={handlePageItemClick} anchorProps={{ rel: 'prev' }}>
                            &lsaquo;
                        </PageItem>

                        {/* Pagination Elements */}
                        {response.elements.map((link, index) => (
                            <PageItemFromLink key={index} link={link} onClick={handlePageItemClick} disabled={false} />
                        ))}

                        {/* Next Page Link */}
                        <PageItem link={response.nextPageUrl ?? '#'} disabled={!response.hasMorePages} onClick={handlePageItemClick} anchorProps={{ rel: 'next' }}>
                            &rsaquo;
                        </PageItem>
                    </ul>
                </div>
            </div>
        </nav>
    );
};

export default Pages;
