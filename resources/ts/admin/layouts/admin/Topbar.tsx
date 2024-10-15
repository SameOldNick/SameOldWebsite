import React from 'react';
import { Nav, Navbar } from 'reactstrap';

import Messages from '@admin/layouts/admin/topbar/Messages';
import Search from '@admin/layouts/admin/topbar/SearchDropdown';
import SearchForm from '@admin/layouts/admin/topbar/SearchForm';
import User from '@admin/layouts/admin/topbar/User';
import MobileNavbar from './topbar/mobile-navbar/MobileNavbar';
import MobileNavbarToggle from './topbar/mobile-navbar/MobileNavbarToggle';

import { createMainMenu } from '@admin/utils/menus/menus';
import NotificationsIcon from './topbar/NotificationsIcon';

interface TopbarProps {

}

const Topbar: React.FC<TopbarProps> = ({ }) => {
    const [mobileNavBarOpen, setMobileNavBarOpen] = React.useState(false);

    const menuItems = createMainMenu();

    return (
        <div className='sticky-top'>
            <Navbar expand light color='white' container={false} className='topbar mb-4 shadow'>
                {/* Mobile Toggle (Topbar) */}
                <MobileNavbarToggle onToggle={() => setMobileNavBarOpen(!mobileNavBarOpen)} />


                {/* Topbar Search */}
                <SearchForm className='d-none d-sm-inline-block form-inline me-auto ms-md-3 my-2 my-md-0 mw-100' />

                <Nav navbar className='ms-auto'>
                    {/* Nav Item - Search Dropdown (Visible Only XS) */}
                    <Search />

                    {/* Nav Item - Messages */}
                    <Messages />
                    {/* Nav Item - Alerts */}
                    <NotificationsIcon />

                    <div className="topbar-divider d-none d-sm-block"></div>

                    {/* Nav Item - User Information */}
                    <User />
                </Nav>
            </Navbar>

            <MobileNavbar items={menuItems} isOpen={mobileNavBarOpen} />

        </div>
    );
}

export default Topbar;
export { TopbarProps };
