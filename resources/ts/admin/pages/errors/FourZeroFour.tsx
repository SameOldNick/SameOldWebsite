import React from 'react';

import Layout, { BigText, Button, Content, Heading, SmallText } from '@admin/layouts/error';
import { FaArrowLeft, FaHome } from 'react-icons/fa';


interface IProps {
}

const FourZeroFour: React.FC<IProps> = ({ }) => {
    const goBack = React.useCallback((e: React.MouseEvent) => {
        e.preventDefault();

        window.history.back();
    }, []);

    const goHome = React.useCallback((e: React.MouseEvent) => {
        e.preventDefault();

        // Need to use window.location, trying to use push from connect-react-router will try to load it in react.
        window.location.href = '/';
    }, []);

    return (
        <Layout title='404 Not Found'>
            <Heading>
                <BigText>Oops!</BigText>
                <SmallText>404 - Page Not Found</SmallText>
            </Heading>

            <Content>
                <p>The page you are looking for might have been removed had its name changed or is temporarily unavailable.</p>

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

export default FourZeroFour;
