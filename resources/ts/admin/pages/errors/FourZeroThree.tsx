import React from 'react';
import { Helmet } from 'react-helmet';
import { connect, ConnectedProps } from 'react-redux';
import { bindActionCreators } from 'redux';

import Layout, { BigText, Button, Content, Heading, SmallText } from '@admin/layouts/error';
import { FaArrowLeft, FaHome } from 'react-icons/fa';

const connector = connect(
    ({ }: RootState) => ({ }),
	(dispatch) => bindActionCreators({ }, dispatch)
);

interface IProps {
}

const FourZeroThree: React.FC<IProps> = ({ }) => {
    const goBack = (e: React.MouseEvent) => {
        e.preventDefault();

        window.history.back();
    }

    const goHome = (e: React.MouseEvent) => {
        e.preventDefault();

        // Need to use window.location, trying to use push from connect-react-router will try to load it in react.
        window.location.href = '/';
    }

    return (
        <Layout title='403 Forbidden'>
            <Heading>
                <BigText>Oops!</BigText>
                <SmallText>403 - Page is Forbidden</SmallText>
            </Heading>

            <Content>
                <p>You do not have permission to access this page.</p>

                <Button href='#' onClick={goBack}>
                    <FaArrowLeft className='me-1' />
                    Go Back
                </Button>

                <SmallText className='my-3'>Or</SmallText>

                <Button href='#' onClick={goHome}>
                    <FaHome className='me-1' />
                    Go To Home Page
                </Button>
            </Content>
        </Layout>
    );
}

export default FourZeroThree;
