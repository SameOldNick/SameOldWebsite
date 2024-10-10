import React from 'react';

import classNames from 'classnames';

import { viewportSize } from '@admin/utils';
import Toggle from './sidebar/Toggle';

interface SidebarProps extends React.PropsWithChildren {
}

const Sidebar: React.FC<SidebarProps> = ({ children }) => {
    const sidebarRef = React.createRef<HTMLUListElement>();
    const [toggled, setToggled] = React.useState(false);

    /**
     * Called when window is resized
     *
     * @private
     * @param {UIEvent} e
     */
    const onResize = React.useCallback((e: UIEvent) => {
        const { width } = viewportSize();

        // Toggle the side navigation when window is resized below 480px
        if (!toggled && width < 576) {
            setToggled(true);
        } else if (toggled && width >= 576) {
            setToggled(false);
        }
    }, [toggled]);

    /**
     * Called when user scrolls sidebar
     *
     * @private
     * @param {WheelEvent} e
     */
    const onScroll = React.useCallback((e: WheelEvent) => {
        const { width } = viewportSize();

        // Prevent the content wrapper from scrolling when the fixed side navigation hovered over
        if (width > 768) {
            e.preventDefault();

            const delta = Math.sign(e.deltaY);

            if (sidebarRef.current)
                sidebarRef.current.scrollTop += (delta < 0 ? 1 : -1) * 30;
        }
    }, [sidebarRef.current]);

    React.useEffect(() => {
        window.addEventListener('resize', onResize);

        if (document.body.classList.contains('fixed-nav') && sidebarRef.current !== null) {
            sidebarRef.current.addEventListener('wheel', onScroll);
        }

        return () => {
            window.removeEventListener('resize', onResize);

            sidebarRef.current?.removeEventListener('wheel', onScroll);
        };
    }, [sidebarRef.current, onScroll, onResize]);

    return (
        <>
            {/* Doesn't use the Nav component from Reactstrap since it doesn't provide a ref to the actual <ul> tag. */}
            <ul
                ref={sidebarRef}
                className={classNames('navbar-nav bg-gradient-primary sidebar sidebar-dark accordion', { 'sidebar-collapsed': toggled })}
                id='accordionSidebar'
            >

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
                <Toggle onToggle={() => setToggled((prev) => !prev)} toggled={toggled} />
            </ul>
        </>
    );
}

export default Sidebar;
export { SidebarProps };
