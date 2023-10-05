import React from 'react';
import { Routes, Route, Navigate } from 'react-router-dom';

import Layout from '@admin/layouts/admin';

import Authenticated from '@admin/middleware/Authenticated';

import SingleSignOn from '@admin/pages/SingleSignOn';
import Dashboard from '@admin/pages/protected/Dashboard';

import FourZeroFour from '@admin/pages/errors/FourZeroFour';
import FourZeroThree from '@admin/pages/errors/FourZeroThree';

import AllPosts from '@admin/pages/protected/posts/All';
import CreatePost from '@admin/pages/protected/posts/Create';
import EditPost from '@admin/pages/protected/posts/Edit';

import AllProjects from '@admin/pages/protected/projects/All';
import CreateProject from '@admin/pages/protected/projects/Create';
import EditProject from '@admin/pages/protected/projects/Edit';

import AllUsers from '@admin/pages/protected/users/All';
import CreateUser from '@admin/pages/protected/users/Create';
import EditUser from '@admin/pages/protected/users/Edit';

import ContactMessages from './protected/contact/Messages';
import ContactSettings from './protected/contact/Settings';

import Profile from './protected/homepage/Profile';
import Skills from './protected/homepage/Skills';
import Technologies from './protected/homepage/Technologies';

const Pages: React.FC = () => {
    return (
        <Routes>
            <Route path='/admin'>
                <Route path='sso/:user' element={<SingleSignOn />} />

                <Route element={<Authenticated errorElement={<FourZeroThree />} />}>
                    <Route element={<Layout />}>
                        <Route path='dashboard' element={<Dashboard />} />

                        <Route path='posts'>
                            <Route path='create' element={<CreatePost />} />
                            <Route path='edit/:article/revisions/:revision' element={<EditPost />} />

                            <Route index element={<AllPosts />} />
                        </Route>

                        <Route path='projects'>
                            <Route path='create' element={<CreateProject />} />
                            <Route path='edit/:project' element={<EditProject />} />
                            <Route index element={<AllProjects />} />
                        </Route>

                        <Route path='contact'>
                            <Route path='messages' element={<ContactMessages />} />
                            <Route path='settings' element={<ContactSettings />} />
                            <Route index element={<Navigate to='messages' />} />
                        </Route>

                        <Route path='homepage'>
                            <Route path='profile' element={<Profile />} />
                            <Route path='skills' element={<Skills />} />
                            <Route path='technologies' element={<Technologies />} />
                            <Route index element={<Navigate to='profile' />} />
                        </Route>

                        <Route path='users'>
                            <Route path='create' element={<CreateUser />} />
                            <Route path='edit/:user' element={<EditUser />} />
                            <Route index element={<AllUsers />} />
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
