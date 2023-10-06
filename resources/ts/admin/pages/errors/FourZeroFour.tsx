import React from 'react';
import { Helmet } from 'react-helmet';
import { connect, ConnectedProps } from 'react-redux';
import { bindActionCreators } from 'redux';

import Layout from '@admin/layouts/error';
import { FaArrowLeft, FaHome } from 'react-icons/fa';

const connector = connect(
    ({ }: RootState) => ({ }),
	(dispatch) => bindActionCreators({ }, dispatch)
);

interface IProps {
}

type TProps = ConnectedProps<typeof connector> & IProps;

interface IState {
}

export default connector(class FourZeroFour extends React.Component<TProps, IState> {
    constructor(props: Readonly<TProps>) {
        super(props);

        this.state = {
        };

        this.goBack = this.goBack.bind(this);
        this.goHome = this.goHome.bind(this);
    }

    private goBack(e: React.MouseEvent) {
        e.preventDefault();

        window.history.back();
    }

    private goHome(e: React.MouseEvent) {
        e.preventDefault();

        // Need to use window.location, trying to use push from connect-react-router will try to load it in react.
        window.location.href = '/';
    }

    public render() {
        return (
            <Layout>
                <Helmet>
                    <title>404 Not Found</title>
                </Helmet>

                <Layout.Heading>
                    <Layout.BigText>Oops!</Layout.BigText>
                    <Layout.SmallText>404 - Page Not Found</Layout.SmallText>
                </Layout.Heading>

                <Layout.Content>
                    <p>The page you are looking for might have been removed had its name changed or is temporarily unavailable.</p>

                    <Layout.Button href='#' onClick={this.goBack}>
                        <FaArrowLeft className='me-1' />
                        Go Back
                    </Layout.Button>

                    <Layout.SmallText className='my-3'>Or</Layout.SmallText>

                    <Layout.Button href='#' onClick={this.goHome}>
                        <FaHome className='me-1' />
                        Go To Home Page
                    </Layout.Button>
                </Layout.Content>
            </Layout>
        );
    }
});
