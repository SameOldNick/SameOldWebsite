import React from 'react';
import { bindActionCreators } from '@reduxjs/toolkit';
import { connect, ConnectedProps } from 'react-redux';
import { Outlet } from 'react-router-dom';
import withReactContent from 'sweetalert2-react-content';

import { DateTime } from 'luxon';
import axios from 'axios';
import Swal from 'sweetalert2';

import accountSlice, { fetchUser as dispatchFetchUser } from '@admin/store/slices/account';

import LoaderOverlay from '../components/Loader';
import Heartbeat, { IHeartbeatCallbackParams } from '../components/Heartbeat';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import PageVisibilityWrapper from '@admin/components/PageVisibilityWrapper';

const connector = connect(
    ({ account }: RootState) => ({ account }),
    (dispatch) => bindActionCreators({ dispatchFetchUser, setUser: accountSlice.actions.setUser }, dispatch)
);

interface IProps {
    errorElement: React.ReactNode;
}

type TProps = ConnectedProps<typeof connector> & React.PropsWithChildren<IProps>;

interface IReauthenticateModal {
    type: 'reauthenticate';
    message: string;
}

type TModals = IReauthenticateModal;

interface IState {
    loading: boolean;
    lastChecked?: DateTime;
    modal?: TModals;
    performHeartbeat: boolean;
}

export default connector(class Authenticated extends React.Component<TProps, IState> {
    constructor(props: Readonly<TProps>) {
        super(props);

        this.state = {
            loading: false,
            performHeartbeat: true
        };

        this.ping = this.ping.bind(this);
    }

    public componentDidMount() {
        this.props.dispatchFetchUser();
    }

    public componentDidUpdate(prevProps: Readonly<TProps>) {
        const { account: { fetchUser, user }, setUser } = this.props;
        const { lastChecked } = this.state;

        if (lastChecked === undefined && (fetchUser.status === 'fulfilled' || fetchUser.status === 'rejected')) {
            this.setState({ lastChecked: DateTime.now() });
        }

        if (fetchUser.status === 'fulfilled' && fetchUser.response && fetchUser.response !== user) {
            setUser(fetchUser.response);
        }
    }

    public componentWillUnmount() {
        this.setState({ lastChecked: undefined });
    }

    private async ping(params: IHeartbeatCallbackParams) {
        try {
            await createAuthRequest().get<IUser>('user');
        } catch (e) {
            await this.onPingFailed(e);
        }
    }

    private async onPingFailed(e: unknown) {
        // Disable heartbeat from displaying alert over and over
        this.setState({ performHeartbeat: false });

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
    }

    public render() {
        const { account: { fetchUser }, errorElement } = this.props;
        const { lastChecked, performHeartbeat } = this.state;

        if (lastChecked === undefined || fetchUser.status === 'pending') {
            return (
                <LoaderOverlay display={{ type: 'page', show: true }} />
            );
        }

        if (fetchUser.status === 'fulfilled') {
            return (
                <>
                    <PageVisibilityWrapper>
                        {(visible) => <Heartbeat active={performHeartbeat && visible} interval={90 * 1000} callback={this.ping} />}
                    </PageVisibilityWrapper>

                    <Outlet />
                </>
            );
        } else {
            return errorElement;
        }
    }
});
