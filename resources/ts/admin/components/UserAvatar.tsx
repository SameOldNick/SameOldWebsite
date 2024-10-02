import React from 'react';

import { DateTime } from 'luxon';
import queryString from 'query-string';

import { createAuthRequest } from '@admin/utils/api/factories';
import Avatar from './Avatar';

interface IAvatarProps extends Omit<React.HTMLProps<HTMLImageElement>, 'ref' | 'src' | 'placeholder' | 'onError'> {
    user: IUser | 'current';
    size?: number;
}

const UserAvatar: React.FC<IAvatarProps> = ({ user, size, style, ...props }) => {
    const [src, setSrc] = React.useState('');
    const [lastRefreshed, setLastRefreshed] = React.useState(DateTime.now());

    React.useEffect(() => {
        fetchAvatar();
    }, [user]);

    const actualSrc = React.useMemo(() => {
        const query = queryString.stringify({
            t: lastRefreshed.toUnixInteger(),
            size
        });

        return `${src}${src.includes('?') ? '&' : '?'}${query}`;
    }, [src, lastRefreshed, size]);

    const fetchAvatar = React.useCallback(async () => {
        try {
            let response;

            if (user === 'current') {
                response = await createAuthRequest().get<IUser>(`/user`);
            } else {
                response = await createAuthRequest().get<IUser>(`/users/${user.id}`);
            }

            setSrc(response.data.avatar_url);
            setLastRefreshed(DateTime.now());
        } catch (err) {
            logger.error(err);
        }
    }, [user]);

    return (
        <Avatar src={actualSrc} style={{ maxHeight: size, ...style }} {...props} />
    );
}

export default UserAvatar;
