import React from 'react';
import { Helmet } from "react-helmet";
import { Outlet } from 'react-router-dom';
import { IconContext } from 'react-icons';
import { FaCloudUploadAlt, FaComments, FaEnvelope, FaHome, FaList, FaNewspaper, FaTachometerAlt, FaUsers } from 'react-icons/fa';
import { Container } from 'reactstrap';

import { DateTime } from 'luxon';

import Sidebar from './Sidebar';
import Item from './sidebar/Item';
import Dropdown from './sidebar/Dropdown';
import DropdownItem from './sidebar/DropdownItem';

import Footer from './Footer';
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
                        <Item href='/admin/dashboard' icon={<FaTachometerAlt />}>Dashboard</Item>

                        <Authorized roles={['edit_profile']}>
                            <Dropdown text='Homepage' icon={<FaHome />}>
                                <DropdownItem href='/admin/homepage/profile'>Edit Profile</DropdownItem>
                                <DropdownItem href='/admin/homepage/skills'>Update Skills</DropdownItem>
                                <DropdownItem href='/admin/homepage/technologies'>Manage Technologies</DropdownItem>
                            </Dropdown>
                        </Authorized>

                        <Authorized roles={['write_posts']}>
                            <Dropdown text='Blog' icon={<FaNewspaper />}>
                                <DropdownItem href='/admin/posts'>View All Posts</DropdownItem>
                                <DropdownItem href='/admin/posts/create'>Create New Post</DropdownItem>
                            </Dropdown>
                        </Authorized>

                        <Authorized roles={['manage_comments']}>
                            <Dropdown text='Comments' icon={<FaComments />}>
                                <DropdownItem href='/admin/comments'>View All Comments</DropdownItem>
                                <DropdownItem href='/admin/comments/settings'>Comment Settings</DropdownItem>
                            </Dropdown>
                        </Authorized>

                        <Authorized roles={['view_contact_messages', 'change_contact_settings']} oneOf>
                            <Dropdown text='Contact' icon={<FaEnvelope />}>
                                <Authorized roles={['view_contact_messages']}>
                                    <DropdownItem href='/admin/contact/messages'>View Messages</DropdownItem>
                                </Authorized>
                                <Authorized roles={['change_contact_settings']}>
                                    <DropdownItem href='/admin/contact/settings'>Settings</DropdownItem>
                                </Authorized>
                            </Dropdown>
                        </Authorized>

                        <Authorized roles={['manage_projects']}>
                            <Dropdown text='Projects' icon={<FaList />}>
                                <DropdownItem href='/admin/projects'>View All Projects</DropdownItem>
                                <DropdownItem href='/admin/projects/create'>Create New Project</DropdownItem>
                            </Dropdown>
                        </Authorized>

                        <Authorized roles={['manage_backups']}>
                            <Dropdown text='Backups' icon={<FaCloudUploadAlt />}>
                                <DropdownItem href='/admin/backups'>View Backups</DropdownItem>
                                <DropdownItem href='/admin/backups/settings'>Backup Settings</DropdownItem>
                            </Dropdown>
                        </Authorized>

                        <Authorized roles={['manage_users']}>
                            <Dropdown text='Users' icon={<FaUsers />}>
                                <DropdownItem href='/admin/users'>View All Users</DropdownItem>
                                <DropdownItem href='/admin/users/create'>Create New User</DropdownItem>
                            </Dropdown>
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
