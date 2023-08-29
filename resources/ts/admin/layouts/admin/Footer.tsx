import React from 'react';
import { Container } from 'reactstrap';

interface IProps {

}

interface IState {
}

export default class extends React.Component<React.PropsWithChildren<IProps>, IState> {
    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
        };
    }

    public render() {
        const { children } = this.props;

        return (
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
    }
}
