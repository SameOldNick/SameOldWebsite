import React from 'react';
import { bindActionCreators } from '@reduxjs/toolkit';
import { connect, ConnectedProps } from 'react-redux';
import { Outlet } from 'react-router-dom';
import withReactContent from 'sweetalert2-react-content';

import { DateTime } from 'luxon';
import axios from 'axios';
import Swal from 'sweetalert2';

import LoaderOverlay from '@admin/components/Loader';
import Heartbeat, { IHeartbeatCallbackParams } from '@admin/components/Heartbeat';
import PageVisibilityWrapper from '@admin/components/PageVisibilityWrapper';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import accountSlice, { fetchUser as dispatchFetchUser } from '@admin/store/slices/account';

const connector = connect(
    ({ account }: RootState) => ({ account }),
    (dispatch) => bindActionCreators({ dispatchFetchUser, setUser: accountSlice.actions.setUser }, dispatch)
);

interface IProps {
    errorElement: React.ReactNode;
}

type TProps = ConnectedProps<typeof connector> & React.PropsWithChildren<IProps>;

const Authenticated: React.FC<TProps> = ({ account: { fetchUser, user }, setUser, dispatchFetchUser, errorElement }) => {
    const [performHeartbeat, setPerformHeartbeat] = React.useState(true);
    const [lastChecked, setLastChecked] = React.useState<DateTime | undefined>();

    const ping = React.useCallback(async (params: IHeartbeatCallbackParams) => {
        try {
            await createAuthRequest().get<IUser>('user');
        } catch (e) {
            await onPingFailed(e);
        }
    }, []);

    const onPingFailed = React.useCallback(async (e: unknown) => {
        // Disable heartbeat from displaying alert over and over
        setPerformHeartbeat(false);

        const message =
            defaultFormatter()
                .addFormatterForStatusCode(401, 'You are no longer logged in. Click "Login" to be redirected to the login page.')
                .addFallbackFormatter('An unknown error occurred determining if you\'re logged in. Click "Login" to be redirected to the login page.')
                    .parse(axios.isAxiosError(e) ? e.response : undefined);

        const result = await withReactContent(Swal).fire({
            icon: 'error',
            title: 'Oops...',
            text: message,
            confirmButtonText: 'Login',
            showCancelButton: true
        });

        if (result.isConfirmed) {
            window.location.href = '/login';
        }
    }, []);

    React.useEffect(() => {
        dispatchFetchUser();
    }, []);

    React.useEffect(() => {
        if (lastChecked === undefined && (fetchUser.status === 'fulfilled' || fetchUser.status === 'rejected')) {
            setLastChecked(DateTime.now());
        }

        if (fetchUser.status === 'fulfilled' && fetchUser.response && fetchUser.response !== user) {
            setUser(fetchUser.response);
        }
    }, [fetchUser]);

    if (lastChecked === undefined || fetchUser.status === 'pending') {
        return (
            <LoaderOverlay display={{ type: 'page', show: true }} />
        );
    }

    if (fetchUser.status === 'fulfilled') {
        return (
            <>
                <PageVisibilityWrapper>
                    {(visible) => <Heartbeat active={performHeartbeat && visible} interval={90 * 1000} callback={ping} />}
                </PageVisibilityWrapper>

                <Outlet />
            </>
        );
    } else {
        return errorElement;
    }
}

export default connector(Authenticated);
