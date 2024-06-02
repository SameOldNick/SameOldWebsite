import React from 'react';
import { ConnectedProps, connect } from 'react-redux';

import PrivateChannel from './PrivateChannel';

interface IUserChannelProps extends React.PropsWithChildren {
}

const connector = connect(
    ({ account }: RootState) => ({ account })
);

type TProps = ConnectedProps<typeof connector> & IUserChannelProps;

const UserChannel: React.FC<TProps> = ({ account, children }) => {
    if (!account.user) {
        logger.error('User does not exist.');

        return null;
    }

    return (
        <>
            <PrivateChannel channel={`App.Models.User.${account.user.id}`}>
                {children}
            </PrivateChannel>
        </>
    );
}

export default connector(UserChannel);
