import React from 'react';
import { Helmet } from 'react-helmet';

import Heading from '@admin/layouts/admin/Heading';

import MessageList from '@admin/components/messages/MessageList';
import { requiresRolesForPage } from '@admin/components/hoc/RequiresRoles';

const Messages: React.FC = () => {
    return (
        <>
            <Helmet>
                <title>Messages</title>
            </Helmet>

            <Heading title='Messages' />

            <MessageList />
        </>
    );
};

export default requiresRolesForPage(Messages, ['view_contact_messages']);
