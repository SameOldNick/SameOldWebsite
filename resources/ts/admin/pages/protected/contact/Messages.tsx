import React from 'react';
import { Helmet } from 'react-helmet';

import Heading from '@admin/layouts/admin/Heading';

import MessageList from '@admin/components/messages/MessageList';

interface IProps {

}

const Messages: React.FC<IProps> = ({ }) => {
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

export default Messages;
