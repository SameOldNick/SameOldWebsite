import React from 'react';
import { Navigate } from 'react-router-dom';
import { connect, ConnectedProps } from 'react-redux';

import account from '@admin/store/slices/account';
import FourZeroThree from './errors/FourZeroThree';

const connector = connect(
    ({ account: { stage } }: RootState) => ({ stage }),
    { setAuthStage: account.actions.authStage }
);

type Props = ConnectedProps<typeof connector> & React.PropsWithChildren;

const SingleSignOn: React.FC<Props> = ({ stage, setAuthStage }) => {
    const [loaded, setLoaded] = React.useState(false);

    React.useEffect(() => {
        const { accessToken, refreshToken } = window;

        if (accessToken && refreshToken) {
            setAuthStage({ stage: 'authenticated', accessToken, refreshToken });
        }

        setLoaded(true);
    }, []);

    if (loaded) {
        return stage.stage === 'authenticated' ? <Navigate to='/admin' replace /> : <FourZeroThree />;
    } else {
        return <></>;
    }
}

export default connector(SingleSignOn);
