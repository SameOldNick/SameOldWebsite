import React from 'react';
import { Col, ColProps, Container, Row } from 'reactstrap';
import { Helmet } from 'react-helmet';
import classNames from 'classnames';
import { IconContext } from 'react-icons';

interface IProps {
    title: string;
}

const Heading: React.FC<React.PropsWithChildren<ColProps>> = ({ children, className, ...props }) => (
    <Col md={12} className={classNames('text-center', className)} {...props}>
        {children}
    </Col>
);

const Content: React.FC<React.PropsWithChildren<ColProps>> = ({ children, className, ...props }) => (
    <Col md={6} className={classNames('text-center', className)} {...props}>
        {children}
    </Col>
);

const BigText: React.FC<React.PropsWithChildren & React.HTMLProps<HTMLHeadingElement>> = ({ children, className, ...props }) => (
    <h1 {...props} className={classNames('big-text bg-dark', className)}>{children}</h1>
);

const SmallText: React.FC<React.PropsWithChildren & React.HTMLProps<HTMLHeadingElement>> = ({ children, className, ...props }) => (
    <h2 {...props} className={classNames('small-text text-uppercase text-dark', className)}>{children}</h2>
);

const Button: React.FC<React.PropsWithChildren & React.HTMLProps<HTMLAnchorElement>> = ({ children, className, ...props }) => (
    <>
        {/*<a {...props} className={classNames('button iq-mt-15 text-center', className)}>{children}</a>*/}
        <a {...props} className={classNames('btn btn-primary fs-6 fw-bolder px-4 py-3 rounded-pill text-uppercase text-center', className)}>{children}</a>
    </>
);

const ErrorLayout: React.FC<React.PropsWithChildren<IProps>> = ({ title, children }) => {
    return (
        <>
            <Helmet>
                <title>{title}</title>
                <body className="error-page" />
            </Helmet>

            <IconContext.Provider value={{ className: 'react-icons' }}>

                <Container className="error-container">
                    <Row className="d-flex align-items-center justify-content-center">
                        {children}
                    </Row>
                </Container>

            </IconContext.Provider>
        </>
    );
}

export { Heading, Content, BigText, SmallText, Button };
export default ErrorLayout;
