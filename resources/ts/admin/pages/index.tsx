import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';

import Layout from '@admin/layouts/admin';

import Authenticated from '@admin/middleware/Authenticated';

import SingleSignOn from '@admin/pages/SingleSignOn';
import Dashboard from '@admin/pages/protected/dashboard/Dashboard';

import FourZeroFour from '@admin/pages/errors/FourZeroFour';
import FourZeroThree from '@admin/pages/errors/FourZeroThree';

import AllPosts from '@admin/pages/protected/articles/All';

import CreateArticle from './protected/articles/Create';
import EditRevision from '@admin/pages/protected/articles/EditRevision';
import EditArticle from '@admin/pages/protected/articles/EditArticle';

import AllComments from '@admin/pages/protected/comments/All';
import EditComments from '@admin/pages/protected/comments/Edit';
import CommentSettings from '@admin/pages/protected/comments/Settings';

import AllProjects from '@admin/pages/protected/projects/All';
import CreateProject from '@admin/pages/protected/projects/Create';
import EditProject from '@admin/pages/protected/projects/Edit';

import AllUsers from '@admin/pages/protected/users/All';
import CreateUser from '@admin/pages/protected/users/Create';
import EditUser from '@admin/pages/protected/users/Edit';

import ContactMessages from './protected/contact/Messages';
import ContactSettings from './protected/contact/Settings';
import ContactBlacklist from './protected/contact/Blacklist';

import Profile from './protected/homepage/Profile';
import Skills from './protected/homepage/Skills';
import Technologies from './protected/homepage/Technologies';

import AllBackups from '@admin/pages/protected/backups/All';
import BackupSettings from '@admin/pages/protected/backups/Settings';

import BackupDestinations from '@admin/pages/protected/backups/destinations/Destinations';
import CreateBackupDestination from '@admin/pages/protected/backups/destinations/Create';
import EditBackupDestination from '@admin/pages/protected/backups/destinations/Edit';

import Notifications from './protected/notifications/Notifications';

const Pages: React.FC = () => {
    return (
        <Routes>
            <Route path='/admin'>
                <Route path='sso/:user' element={<SingleSignOn />} />

                <Route element={<Authenticated errorElement={<FourZeroThree />} />}>
                    <Route element={<Layout />}>
                        <Route path='dashboard' element={<Dashboard />} />
                        <Route path='notifications' element={<Notifications />} />

                        <Route path='homepage'>
                            <Route path='profile' element={<Profile />} />
                            <Route path='skills' element={<Skills />} />
                            <Route path='technologies' element={<Technologies />} />
                            <Route index element={<Navigate to='profile' />} />
                        </Route>

                        <Route path='posts'>
                            <Route path='create' element={<CreateArticle />} />
                            <Route path='edit/:article' element={<EditArticle />} />
                            <Route path='edit/:article/revisions/:revision' element={<EditRevision />} />

                            <Route index element={<AllPosts />} />
                        </Route>

                        <Route path='comments'>
                            <Route path='edit/:comment' element={<EditComments />} />
                            <Route path='settings' element={<CommentSettings />} />
                            <Route index element={<AllComments />} />
                        </Route>

                        <Route path='contact'>
                            <Route path='messages' element={<ContactMessages />} />
                            <Route path='settings' element={<ContactSettings />} />
                            <Route path='blacklist' element={<ContactBlacklist />} />
                            <Route index element={<Navigate to='messages' />} />
                        </Route>

                        <Route path='projects'>
                            <Route path='create' element={<CreateProject />} />
                            <Route path='edit/:project' element={<EditProject />} />
                            <Route index element={<AllProjects />} />
                        </Route>

                        <Route path='users'>
                            <Route path='create' element={<CreateUser />} />
                            <Route path='edit/:user' element={<EditUser />} />
                            <Route index element={<AllUsers />} />
                        </Route>

                        <Route path='backups'>
                            <Route index element={<AllBackups />} />
                            <Route path='settings' element={<BackupSettings />} />

                            <Route path='destinations'>
                                <Route path='create' element={<CreateBackupDestination />} />
                                <Route path='edit/:destination' element={<EditBackupDestination />} />

                                <Route index element={<BackupDestinations />} />
                            </Route>

                        </Route>


                        <Route index element={<Navigate to='dashboard' />} />
                    </Route>
                </Route>

                <Route path='*' element={<FourZeroFour />} />
            </Route>
        </Routes>
    );
}

export default Pages;
