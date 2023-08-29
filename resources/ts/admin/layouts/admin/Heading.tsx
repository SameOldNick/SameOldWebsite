import React from 'react';

interface IProps {
    title?: string;
}

interface IState {
}

export default class Heading extends React.Component<React.PropsWithChildren<IProps>, IState> {
    public static Title: React.FC<React.PropsWithChildren> = ({ children }) => (
        <h1 className="h3 mb-0 text-gray-800">{children}</h1>
    );

    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
        };
    }

    public render() {
        const { title, children } = this.props;

        return (
            <>
                <div className="d-sm-flex align-items-center justify-content-between mb-4">
                    {title && <Heading.Title>{title}</Heading.Title>}

                    {children}
                </div>
            </>
        );
    }
}
