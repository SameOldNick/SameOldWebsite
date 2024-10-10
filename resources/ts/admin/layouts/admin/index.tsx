import React from 'react';
import { Helmet } from "react-helmet";
import { Outlet } from 'react-router-dom';
import { IconContext } from 'react-icons';
import { FaCloudUploadAlt, FaComments, FaEnvelope, FaHome, FaList, FaNewspaper, FaTachometerAlt, FaUsers } from 'react-icons/fa';
import { Container } from 'reactstrap';

import { DateTime } from 'luxon';

import Footer from './Footer';
import Sidebar from './Sidebar';
import Topbar from './Topbar';
import ScrollToTop from './ScrollToTop';
import Authorized from '@admin/middleware/Authorized';

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

                        <Authorized roles={['edit_profile']}>
                            <Sidebar.Dropdown text='Homepage' icon={<FaHome />}>
                                <Sidebar.DropdownItem href='/admin/homepage/profile'>Edit Profile</Sidebar.DropdownItem>
                                <Sidebar.DropdownItem href='/admin/homepage/skills'>Update Skills</Sidebar.DropdownItem>
                                <Sidebar.DropdownItem href='/admin/homepage/technologies'>Manage Technologies</Sidebar.DropdownItem>
                            </Sidebar.Dropdown>
                        </Authorized>

                        <Authorized roles={['write_posts']}>
                            <Sidebar.Dropdown text='Blog' icon={<FaNewspaper />}>
                                <Sidebar.DropdownItem href='/admin/posts'>View All Posts</Sidebar.DropdownItem>
                                <Sidebar.DropdownItem href='/admin/posts/create'>Create New Post</Sidebar.DropdownItem>
                            </Sidebar.Dropdown>
                        </Authorized>

                        <Authorized roles={['manage_comments']}>
                            <Sidebar.Dropdown text='Comments' icon={<FaComments />}>
                                <Sidebar.DropdownItem href='/admin/comments'>View All Comments</Sidebar.DropdownItem>
                                <Sidebar.DropdownItem href='/admin/comments/settings'>Comment Settings</Sidebar.DropdownItem>
                            </Sidebar.Dropdown>
                        </Authorized>

                        <Authorized roles={['view_contact_messages', 'change_contact_settings']} oneOf>
                            <Sidebar.Dropdown text='Contact' icon={<FaEnvelope />}>
                                <Authorized roles={['view_contact_messages']}>
                                    <Sidebar.DropdownItem href='/admin/contact/messages'>View Messages</Sidebar.DropdownItem>
                                </Authorized>
                                <Authorized roles={['change_contact_settings']}>
                                    <Sidebar.DropdownItem href='/admin/contact/settings'>Settings</Sidebar.DropdownItem>
                                </Authorized>
                            </Sidebar.Dropdown>
                        </Authorized>

                        <Authorized roles={['manage_projects']}>
                            <Sidebar.Dropdown text='Projects' icon={<FaList />}>
                                <Sidebar.DropdownItem href='/admin/projects'>View All Projects</Sidebar.DropdownItem>
                                <Sidebar.DropdownItem href='/admin/projects/create'>Create New Project</Sidebar.DropdownItem>
                            </Sidebar.Dropdown>
                        </Authorized>

                        <Authorized roles={['manage_backups']}>
                            <Sidebar.Dropdown text='Backups' icon={<FaCloudUploadAlt />}>
                                <Sidebar.DropdownItem href='/admin/backups'>View Backups</Sidebar.DropdownItem>
                                <Sidebar.DropdownItem href='/admin/backups/settings'>Backup Settings</Sidebar.DropdownItem>
                            </Sidebar.Dropdown>
                        </Authorized>

                        <Authorized roles={['manage_users']}>
                            <Sidebar.Dropdown text='Users' icon={<FaUsers />}>
                                <Sidebar.DropdownItem href='/admin/users'>View All Users</Sidebar.DropdownItem>
                                <Sidebar.DropdownItem href='/admin/users/create'>Create New User</Sidebar.DropdownItem>
                            </Sidebar.Dropdown>
                        </Authorized>
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
                            <Container fluid>
                                <Outlet />
                            </Container>
                            {/* /.container-fluid */}

                        </div>
                        {/* End of Main Content */}

                        {/* Footer */}
                        <Footer>
                            Copyright &copy; <a href='https://www.sameoldnick.com' target='_blank'>Same Old Nick</a> {DateTime.now().year}
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
