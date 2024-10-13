import React from 'react';

import { DateTime } from 'luxon';
import queryString from 'query-string';

import Avatar from './Avatar';
import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';

import User from '@admin/utils/api/models/User';

import { createAuthRequest } from '@admin/utils/api/factories';
import createErrorHandler from '@admin/utils/errors/factory';

interface AvatarProps extends Omit<React.HTMLProps<HTMLImageElement>, 'ref' | 'src' | 'placeholder' | 'onError'> {
    userId: number;
    size?: number;
}

const UserAvatar: React.FC<AvatarProps> = ({ userId, size, style, ...props }) => {
    const waitToLoadRef = React.useRef<IWaitToLoadHandle>(null);

    const createSrc = React.useCallback((src: string) => {
        const query = queryString.stringify({
            t: DateTime.now().toUnixInteger(),
            size
        });

        return `${src}${src.includes('?') ? '&' : '?'}${query}`;
    }, [size]);


    const load = React.useCallback(async () => {
        const response = await createAuthRequest().get<IUser>(`/users/${userId}`);

        return new User(response.data);
    }, [userId]);

    const handleLoadError = React.useCallback((error: unknown) => {
        const message = createErrorHandler().handle(error);

        logger.error(`Unable to load avatar: ${message}`);

        return null;
    }, []);

    return (
        <>
            <WaitToLoad ref={waitToLoadRef} callback={load} loading={<Loader display={{ type: 'over-element' }} />}>
                {(response, err) => (
                    <>
                        {response && <Avatar src={createSrc(response.user.avatar_url)} style={{ maxHeight: size, ...style }} {...props} />}
                        {err && handleLoadError(err)}
                    </>
                )}
            </WaitToLoad>
        </>
    );
}

export default UserAvatar;
