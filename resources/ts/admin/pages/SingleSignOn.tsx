import React from 'react';
import { Navigate } from 'react-router-dom';
import { connect, ConnectedProps } from 'react-redux';
import { bindActionCreators } from 'redux';

import account from '@admin/store/slices/account';

const connector = connect(
    ({ account: { stage } }: RootState) => ({ stage }),
    (dispatch) => bindActionCreators({
        setAuthStage: account.actions.authStage
    }, dispatch)
);

interface IProps {
}

type TProps = ConnectedProps<typeof connector> & React.PropsWithChildren<IProps>;

interface IState {
}

export default connector(class SingleSignOn extends React.Component<TProps, IState> {
    constructor(props: Readonly<TProps>) {
        super(props);

        this.state = {
        };
    }

    public componentDidMount() {
        const { setAuthStage } = this.props;
        const { accessToken, refreshToken } = window;

        if (accessToken && refreshToken) {
            setAuthStage({ stage: 'authenticated', accessToken, refreshToken });
        }
    }

    public render() {
        const { stage } = this.props;
        const { } = this.state;

        return (
            <>
                {stage.stage === 'authenticated' ? <Navigate to='/admin' replace /> : undefined}
            </>
        );
    }
});
