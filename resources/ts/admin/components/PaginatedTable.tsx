import React from 'react';
import classNames from 'classnames';

import PaginateResponse from '@admin/utils/api/models/PaginateResponse';

type TChildrenCallback<TData = unknown> = (data: TData[]) => React.ReactNode;

interface IProps<TData = unknown> {
    initialResponse: IPaginateResponse<TData>;
    pullData: (link?: string) => Promise<IPaginateResponse<TData>>;
    onUpdate?: (data: TData[]) => void;
    onError?: (e: unknown) => void;
    children: React.ReactNode | TChildrenCallback<TData>;
}

interface IState {
    lastLink?: string;
    lastResponse: PaginateResponse;
}

interface IPageItemProps extends Omit<React.HTMLProps<HTMLLIElement>, 'onClick'> {
    link?: string;
    onClick: (e: React.MouseEvent, link: string) => void;
    disabled?: boolean;
    active?: boolean;
    anchorProps?: React.HTMLProps<HTMLAnchorElement>;
}

interface IPageItemFromLinkProps extends Omit<React.HTMLProps<HTMLLIElement>, 'onClick'> {
    link: IPaginateResponseLink;
    onClick: (e: React.MouseEvent, link: string) => void;
    anchorProps?: React.HTMLProps<HTMLAnchorElement>;
}

export default class PaginatedTable<TData> extends React.Component<IProps<TData>, IState> {
    /**
     * PageItem component
     *
     * @static
     * @type {React.FC<React.PropsWithChildren<IPageItemProps>>}
     * @memberof PaginatedTable
     */
    public static PageItem: React.FC<React.PropsWithChildren<IPageItemProps>> = ({ children, disabled, active, link, onClick, anchorProps, ...props }) => {
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

    /**
     * Page Item from IPaginateResponseLink component
     *
     * @static
     * @type {React.FC<IPageItemFromLinkProps>}
     * @memberof PaginatedTable
     */
    public static PageItemFromLink: React.FC<IPageItemFromLinkProps> = ({ link: { url, active, label }, onClick, ...props }) => {
        return (
            <PaginatedTable.PageItem link={url ?? undefined} active={active} onClick={onClick} {...props}>
                {label}
            </PaginatedTable.PageItem>
        );
    };

    /**
     * Creates an instance of PaginatedTable.
     * @param {Readonly<IProps<TData>>} props
     * @memberof PaginatedTable
     */
    constructor(props: Readonly<IProps<TData>>) {
        super(props);

        this.state = {
            lastResponse: new PaginateResponse(props.initialResponse)
        };

        this.pullData = this.pullData.bind(this);
        this.onPageItemClick = this.onPageItemClick.bind(this);
    }

    public componentDidMount() {
        const { initialResponse } = this.props;
        const { lastResponse } = this.state;

        if (initialResponse !== lastResponse.response) {
            this.setState({ lastResponse: new PaginateResponse(initialResponse) });
        }
    }

    public componentDidUpdate(prevProps: Readonly<IProps<TData>>) {
        const { initialResponse } = this.props;

        if (initialResponse !== prevProps.initialResponse) {
            this.setState({ lastResponse: new PaginateResponse(initialResponse) });
        }
    }

    /**
     * Called when page item component is clicked
     *
     * @private
     * @param {React.MouseEvent} e
     * @param {string} link
     * @memberof PaginatedTable
     */
    private onPageItemClick(e: React.MouseEvent, link: string) {
        e.preventDefault();

        this.setState({ lastLink: link }, () => this.pullData(link));
    }

    /**
     * Pulls data and updates state (as well as calls onUpdate prop)
     *
     * @private
     * @param {string} [link]
     * @memberof PaginatedTable
     */
    private async pullData(link: string) {
        const { pullData, onError, onUpdate } = this.props;

        try {
            const response = await pullData(link);

            this.setState({
                lastResponse: new PaginateResponse(response)
            }, () => onUpdate?.call(undefined, response.data));
        } catch (e) {
            if (onError)
                onError(e);
        }
    }

    /**
     * Checks if callback was passed as children
     * @param children Children
     * @returns True if is callback
     */
    private isChildrenCallback(children: any): children is TChildrenCallback {
        return typeof children === 'function';
    }

    /**
     * Renders children
     *
     * @private
     * @template C
     * @param {C} children
     * @param {PaginateResponse} response
     * @returns Rendered children
     * @memberof PaginatedTable
     */
    private renderChildren<C>(children: C, response: PaginateResponse) {
        if (this.isChildrenCallback(children)) {
            return children(response.response.data);
        } else {
            return children;
        }
    }

    /**
     * Renders bottom of pagination table
     *
     * @private
     * @param {PaginateResponse} response
     * @returns Bottom components
     * @memberof PaginatedTable
     */
    private renderBottom(response: PaginateResponse) {
        return (
            <nav className='d-flex justify-items-center justify-content-between'>
                <div className="d-flex justify-content-between flex-fill d-sm-none">
                    <ul className="pagination">
                        {/* Previous Page Link */}
                        <PaginatedTable.PageItem link={response.previousPageUrl ?? '#'} disabled={response.onFirstPage} onClick={this.onPageItemClick} anchorProps={{ rel: 'prev' }}>
                            &laquo; Previous
                        </PaginatedTable.PageItem>

                        {/* Next Page Link */}
                        <PaginatedTable.PageItem link={response.nextPageUrl ?? '#'} disabled={!response.hasMorePages} onClick={this.onPageItemClick} anchorProps={{ rel: 'next' }}>
                            Next &raquo;
                        </PaginatedTable.PageItem>
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
                            <PaginatedTable.PageItem link={response.previousPageUrl ?? '#'} disabled={response.onFirstPage} onClick={this.onPageItemClick} anchorProps={{ rel: 'prev' }}>
                                &lsaquo;
                            </PaginatedTable.PageItem>

                            {/* Pagination Elements */}
                            {response.elements.map((link, index) => (
                                <PaginatedTable.PageItemFromLink key={index} link={link} onClick={this.onPageItemClick} disabled={false} />
                            ))}

                            {/* Next Page Link */}
                            <PaginatedTable.PageItem link={response.nextPageUrl ?? '#'} disabled={!response.hasMorePages} onClick={this.onPageItemClick} anchorProps={{ rel: 'next' }}>
                                &rsaquo;
                            </PaginatedTable.PageItem>
                        </ul>
                    </div>
                </div>
            </nav>
        );
    }

    public reload() {
        const { lastLink } = this.state;

        if (lastLink)
            this.pullData(lastLink);
        else
            this.forceUpdate();
    }

    public render() {
        const { children } = this.props;
        const { lastResponse } = this.state;

        return (
            <>
                {this.renderChildren(children, lastResponse)}

                {lastResponse.hasPages && this.renderBottom(lastResponse)}
            </>
        );
    }
}
