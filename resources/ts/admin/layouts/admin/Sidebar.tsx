import React from 'react';
import { IconContext } from 'react-icons';
import { Collapse, NavItem, NavLink } from 'reactstrap';
import { NavLink as ReactRouterNavLink, useLocation } from "react-router-dom";
import { FaAngleDown, FaAngleLeft, FaAngleRight, FaAngleUp } from 'react-icons/fa';

import classNames from 'classnames';

import { viewportSize } from '@admin/utils';

interface IProps extends React.PropsWithChildren {
}

type TProps = React.PropsWithChildren<IProps>;

interface IState {
    toggled: boolean;
}

interface IToggleProps {
    onToggle: () => void;
    toggled: boolean;
}

export interface IMenuItemProps extends React.PropsWithChildren {
    href: string;
    icon?: React.ReactNode;
    exact?: boolean;
}

export interface IDropdownMenuItemProps extends React.PropsWithChildren {
    href: string;
    icon?: React.ReactNode;
    exact?: boolean;
}

export interface IDropdownMenuProps extends React.PropsWithChildren {
    icon?: React.ReactNode;
    text: string;
}

export interface IDividerItem {
    divider: true;
}

interface IDropdownContext {
    inDropdown: boolean;
    hasActive: boolean;
    setHasActive: (active: boolean) => void;
}

const DropdownContext = React.createContext<IDropdownContext>({ inDropdown: false, hasActive: false, setHasActive: () => null });

export default class Sidebar extends React.Component<TProps, IState> {
    private static locationMatches = (location: ReturnType<typeof useLocation>, href: string, exact: boolean = false) => {
        return exact ? location.pathname === href : location.pathname.startsWith(href) && location.pathname.charAt(href.length) === '/';
    }

    private static Icon: React.FC<React.PropsWithChildren> = ({ children }) => {
        if (children === undefined)
            return <></>;

        return (
            <IconContext.Provider value={{ className: "fa-fw" }}>
                {children}
            </IconContext.Provider>
        );
    }

    private static Toggle: React.FC<IToggleProps> = ({ toggled, onToggle }) => {
        const toggle = React.useCallback((e: React.MouseEvent) => {
            e.preventDefault();

            onToggle();
        }, [onToggle]);

        return (
            <div className="text-center d-none d-md-inline">
                <button id="sidebarToggle" className="rounded-circle border-0" onClick={toggle}>
                    {toggled ? <FaAngleRight /> : <FaAngleLeft />}
                </button>
            </div>
        );
    }

    public static Item: React.FC<IMenuItemProps> = ({ icon, href, exact, children }) => {
        return (
            <NavItem>
                <NavLink tag={ReactRouterNavLink} to={href} end={exact}>
                    <Sidebar.Icon>
                        {icon}
                    </Sidebar.Icon>

                    <span style={{ verticalAlign: 'middle' }}>
                        {children}
                    </span>
                </NavLink>
            </NavItem>
        );
    }

    public static Dropdown: React.FC<IDropdownMenuProps> = ({ icon, text, children }) => {
        const [hasActive, setHasActive] = React.useState(false);
        const [isOpen, setIsOpen] = React.useState(false);

        React.useEffect(() => {
            if (hasActive && !isOpen)
                setIsOpen(true);
        }, [hasActive]);

        const onResize = React.useCallback(() => {
            const { width } = viewportSize();

            // Close any open menu accordions when window is resized below 768px
            if (width < 768 && isOpen) {
                setIsOpen(false);
            }
        }, [isOpen]);

        React.useEffect(() => {
            window.addEventListener('resize', onResize);

            return () => window.removeEventListener('resize', onResize);
        }, [onResize]);

        return (
            <DropdownContext.Provider value={{ hasActive, setHasActive, inDropdown: true }}>
                <NavItem>
                    <NavLink href='#' className={classNames('d-flex justify-content-between', { collapsed: !isOpen })} role='button' onClick={() => setIsOpen(!isOpen)}>
                        <span>
                            <Sidebar.Icon>
                                {icon}
                            </Sidebar.Icon>

                            <span style={{ verticalAlign: 'middle' }}>
                                {text}
                            </span>
                        </span>

                        <span>
                            {isOpen ? <FaAngleUp /> : <FaAngleDown />}
                        </span>
                    </NavLink>
                    <Collapse isOpen={isOpen}>
                        <ul className="py-2 collapse-inner">
                            {children}
                        </ul>
                    </Collapse>
                </NavItem>
            </DropdownContext.Provider>

        );
    }

    public static DropdownItem: React.FC<IDropdownMenuItemProps> = ({ icon, exact, href, children }) => {
        const { setHasActive, inDropdown } = React.useContext(DropdownContext);
        const [isActive, setIsActive] = React.useState(false);

        const location = useLocation();

        if (!inDropdown) {
            logger.error('DropdownItem component must be inside Dropdown component.');
            return;
        }

        React.useEffect(() => {
            const pathMatches = Sidebar.locationMatches(location, href, exact !== undefined ? exact : true);

            if (pathMatches) {
                if (!isActive)
                    setIsActive(true);

                setHasActive(true);
            } else {
                if (isActive)
                    setIsActive(false);
            }

            return () => setIsActive(false);
        }, [location, href, exact]);

        return (
            <>
                {/* Using a function for classNames causes active class to not be automatically applied. */}
                <ReactRouterNavLink end={false} to={href} className={() => classNames('my-1 collapse-item', { active: isActive })}>
                    <Sidebar.Icon>
                        {icon}
                    </Sidebar.Icon>

                    <span>{children}</span>
                </ReactRouterNavLink>
            </>
        );
    }

    public static Divider: React.FC = ({ }) => (
        <hr className="sidebar-divider" />
    );

    private readonly sidebarRef: React.RefObject<HTMLUListElement>;

    constructor(props: Readonly<TProps>) {
        super(props);

        this.state = {
            toggled: false
        };

        this.sidebarRef = React.createRef();

        this.onToggle = this.onToggle.bind(this);
        this.onResize = this.onResize.bind(this);
        this.onScroll = this.onScroll.bind(this);
    }

    public componentDidMount() {
        window.addEventListener('resize', this.onResize);

        if (document.body.classList.contains('fixed-nav') && this.sidebarRef.current !== null) {
            this.sidebarRef.current.addEventListener('wheel', this.onScroll);
        }
    }

    public componentWillUnmount() {
        window.removeEventListener('resize', this.onResize);

        this.sidebarRef.current?.removeEventListener('wheel', this.onScroll);
    }

    /**
     * Called when window is resized
     *
     * @private
     * @param {UIEvent} e
     */
    private onResize(e: UIEvent) {
        const { toggled } = this.state;
        const { width } = viewportSize();

        // Toggle the side navigation when window is resized below 480px
        if (!toggled && width < 576) {
            this.setState({ toggled: true });
        } else if (toggled && width >= 576) {
            this.setState({ toggled: false });
        }
    }

    /**
     * Called when user scrolls sidebar
     *
     * @private
     * @param {WheelEvent} e
     */
    private onScroll(e: WheelEvent) {
        const { width } = viewportSize();

        // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
        if (width > 768) {
            e.preventDefault();

            const delta = Math.sign(e.deltaY);

            if (this.sidebarRef.current)
                this.sidebarRef.current.scrollTop += (delta < 0 ? 1 : -1) * 30;
        }
    }

    private onToggle() {
        this.setState(({ toggled }) => ({ toggled: !toggled }));
    }

    public render() {
        const { children } = this.props;
        const { toggled } = this.state;

        return (
            <>
                {/* Doesn't use the Nav component from Reactstrap since it doesn't provide a ref to the actual <ul> tag. */}
                <ul ref={this.sidebarRef} className={classNames('navbar-nav bg-gradient-primary sidebar sidebar-dark accordion', { 'sidebar-collapsed': toggled })} id='accordionSidebar'>

                    {/* Sidebar - Brand */}
                    <a className="sidebar-brand d-flex align-items-center justify-content-center" href="/admin">
                        <div className="sidebar-brand-icon">
                            {/*<img src={icon} alt="Little Apps" className="img-fluid" />*/}
                        </div>
                        <div className="sidebar-brand-text mx-1">Same Old Nick</div>
                    </a>

                    {children}

                    {/* Divider */}
                    <hr className="sidebar-divider d-none d-md-block" />

                    {/* Sidebar Toggler (Sidebar) */}
                    <Sidebar.Toggle onToggle={this.onToggle} toggled={toggled} />
                </ul>
            </>
        );
    }
}
