import React from 'react';
import { Helmet } from "react-helmet";
import { Outlet } from 'react-router-dom';
import { IconContext } from 'react-icons';
import { FaComments, FaEnvelope, FaHome, FaList, FaNewspaper, FaTachometerAlt, FaUsers } from 'react-icons/fa';

import { DateTime } from 'luxon';

import Footer from './Footer';
import Sidebar from './Sidebar';
import Topbar from './Topbar';
import ScrollToTop from './ScrollToTop';

interface IProps {
}

type TProps = React.PropsWithChildren<IProps>;

const AdminLayout: React.FC<TProps> = ({ }) => {
    return (
        <>
            <Helmet>
                <body id="pageTop" />
            </Helmet>

            <IconContext.Provider value={{ className: 'react-icons' }}>

                {/* Page Wrapper */}
                <div id="wrapper">

                    {/* Sidebar */}
                    <Sidebar>
                        <Sidebar.Item href='/admin/dashboard' icon={<FaTachometerAlt />}>Dashboard</Sidebar.Item>

                        <Sidebar.Dropdown text='Homepage' icon={<FaHome />}>
                            <Sidebar.DropdownItem href='/admin/homepage/profile'>Edit Profile</Sidebar.DropdownItem>
                            <Sidebar.DropdownItem href='/admin/homepage/skills'>Update Skills</Sidebar.DropdownItem>
                            <Sidebar.DropdownItem href='/admin/homepage/technologies'>Manage Technologies</Sidebar.DropdownItem>
                        </Sidebar.Dropdown>

                        <Sidebar.Dropdown text='Blog' icon={<FaNewspaper />}>
                            <Sidebar.DropdownItem href='/admin/posts'>View All Posts</Sidebar.DropdownItem>
                            <Sidebar.DropdownItem href='/admin/posts/create'>Create New Post</Sidebar.DropdownItem>
                        </Sidebar.Dropdown>

                        <Sidebar.Dropdown text='Comments' icon={<FaComments />}>
                            <Sidebar.DropdownItem href='/admin/comments'>View All Comments</Sidebar.DropdownItem>
                        </Sidebar.Dropdown>

                        <Sidebar.Dropdown text='Contact' icon={<FaEnvelope />}>
                            <Sidebar.DropdownItem href='/admin/contact/messages'>View Messages</Sidebar.DropdownItem>
                            <Sidebar.DropdownItem href='/admin/contact/settings'>Settings</Sidebar.DropdownItem>
                        </Sidebar.Dropdown>

                        <Sidebar.Dropdown text='Projects' icon={<FaList />}>
                            <Sidebar.DropdownItem href='/admin/projects'>View All Projects</Sidebar.DropdownItem>
                            <Sidebar.DropdownItem href='/admin/projects/create'>Create New Project</Sidebar.DropdownItem>
                        </Sidebar.Dropdown>

                        <Sidebar.Dropdown text='Users' icon={<FaUsers />}>
                            <Sidebar.DropdownItem href='/admin/users'>View All Users</Sidebar.DropdownItem>
                            <Sidebar.DropdownItem href='/admin/users/create'>Create New User</Sidebar.DropdownItem>
                        </Sidebar.Dropdown>
                    </Sidebar>
                    {/* End of Sidebar */}

                    {/* Content Wrapper */}
                    <div id="content-wrapper" className="d-flex flex-column">

                        {/* Main Content */}
                        <div id="content">

                            {/* Topbar */}
                            <Topbar />
                            {/* End of Topbar */}

                            {/* Begin Page Content */}
                            <div className="container-fluid">
                                <Outlet />
                            </div>
                            {/* /.container-fluid */}

                        </div>
                        {/* End of Main Content */}

                        {/* Footer */}
                        <Footer>
                            Copyright &copy; Little Apps, Ltd. {DateTime.now().year}
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
