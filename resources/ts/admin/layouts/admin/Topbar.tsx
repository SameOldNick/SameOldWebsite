import React from 'react';
import { Nav, Navbar } from 'reactstrap';

import Messages from '@admin/layouts/admin/topbar/Messages';
import Search from '@admin/layouts/admin/topbar/SearchDropdown';
import SearchForm from '@admin/layouts/admin/topbar/SearchForm';
import SidebarToggle from '@admin/layouts/admin/topbar/SidebarToggle';
import User from '@admin/layouts/admin/topbar/User';

interface IProps {

}

const Topbar: React.FC<IProps> = ({ }) => (
    <>
        <Navbar expand light color='white' container={false} className='topbar mb-4 static-top shadow'>
            {/* Sidebar Toggle (Topbar) */}
            <SidebarToggle />

            {/* Topbar Search */}
            <SearchForm className='d-none d-sm-inline-block form-inline me-auto ms-md-3 my-2 my-md-0 mw-100' />

            <Nav navbar className='ms-auto'>
                {/* Nav Item - Search Dropdown (Visible Only XS) */}
                <Search />

                {/* Nav Item - Alerts */}
                {/*<Alerts />*/}

                {/* Nav Item - Messages */}
                <Messages />

                <div className="topbar-divider d-none d-sm-block"></div>

                {/* Nav Item - User Information */}
                <User />
            </Nav>
        </Navbar>

    </>
);

export default Topbar;
