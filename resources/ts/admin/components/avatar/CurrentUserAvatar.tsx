import React from 'react';
import { connect, ConnectedProps } from 'react-redux';

import { DateTime } from 'luxon';
import queryString from 'query-string';

import Avatar from './Avatar';

import { fetchUser } from '@admin/store/slices/account';

const connector = connect(
    ({ account }: RootState) => ({ account }),
    { fetchUser }
);

interface ICurrentUserAvatarProps extends Omit<React.HTMLProps<HTMLImageElement>, 'ref' | 'src' | 'placeholder' | 'onError'> {
    size?: number;
}

type CurrentUserAvatarProps = ICurrentUserAvatarProps & ConnectedProps<typeof connector>

const CurrentUserAvatar: React.FC<CurrentUserAvatarProps> = ({ size, style, fetchUser, account, ...props }) => {
    const [hasFetched, setHasFetched] = React.useState(false);

    const createSrc = React.useCallback((src: string) => {
        const query = queryString.stringify({
            t: DateTime.now().toUnixInteger(),
            size
        });

        return `${src}${src.includes('?') ? '&' : '?'}${query}`;
    }, [size]);

    if (!account.user) {
        if (!hasFetched) {
            fetchUser();

            setHasFetched(true);
        }

        return null;
    }

    return (
        <>
            <Avatar src={createSrc(account.user.user.avatar_url)} style={{ maxHeight: size, ...style }} {...props} />
        </>
    );
}

export default connector(CurrentUserAvatar);
