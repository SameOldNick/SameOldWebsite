import React from 'react';
import { Container } from 'reactstrap';

const Footer: React.FC<React.PropsWithChildren> = ({ children }) => (
    <>
        <footer className="sticky-footer bg-white mt-3">
            <Container className="my-auto py-3">
                <div className="copyright text-center my-auto">
                    <span>{children}</span>
                </div>
            </Container>
        </footer>
    </>
);

export default Footer;
