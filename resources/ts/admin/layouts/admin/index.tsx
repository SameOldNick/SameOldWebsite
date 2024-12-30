import React from 'react';
import { Helmet } from "react-helmet-async";
import { Outlet } from 'react-router-dom';
import { IconContext } from 'react-icons';
import { Container } from 'reactstrap';

import { DateTime } from 'luxon';

import Footer from './Footer';
import Topbar from './Topbar';
import ScrollToTop from './ScrollToTop';
import { createMainMenu } from '@admin/utils/menus/menus';
import SidebarWrapper from './sidebar/wrapper/SidebarWrapper';

const AdminLayout: React.FC<React.PropsWithChildren> = () => {
    const menuItems = createMainMenu();

    return (
        <>
            <Helmet>
                <body id="pageTop" />
            </Helmet>

            <IconContext.Provider value={{ className: 'react-icons' }}>

                {/* Page Wrapper */}
                <div id="wrapper">

                    {/* Sidebar */}
                    <SidebarWrapper items={menuItems} />
                    {/* End of Sidebar */}

                    {/* Content Wrapper */}
                    <div id="content-wrapper" className="d-flex flex-column">

                        {/* Main Content */}
                        <div id="content">

                            {/* Topbar */}
                            <Topbar />
                            {/* End of Topbar */}

                            {/* Begin Page Content */}
                            <Container fluid>
                                <Outlet />
                            </Container>
                            {/* /.container-fluid */}

                        </div>
                        {/* End of Main Content */}

                        {/* Footer */}
                        <Footer>
                            Copyright &copy; <a href='https://www.sameoldnick.com' target='_blank' rel="noreferrer">Same Old Nick</a> {DateTime.now().year}
                        </Footer>
                        {/* End of Footer */}

                    </div>
                    {/* End of Content Wrapper */}

                </div>
                {/* End of Page Wrapper */}

                {/* Scroll to Top Button*/}
                <ScrollToTop scrollTo='pageTop' />

            </IconContext.Provider>

        </>
    );
}

export default AdminLayout;
