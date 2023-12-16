import classNames from 'classnames';
import React from 'react';
import { Card as ReactstrapCard, CardProps, CardBody, CardBodyProps, CardHeader, CardHeaderProps } from 'reactstrap';

interface ICardProps extends React.PropsWithChildren<CardProps> {

}

interface ICardHeaderProps extends React.PropsWithChildren<CardHeaderProps> {

}

interface ICardBodyProps extends React.PropsWithChildren<CardBodyProps> {

}

export default class Card extends React.Component<ICardProps> {
    public static Header: React.FC<ICardHeaderProps> = ({ className, children, ...props }) => (
        <CardHeader className={classNames("py-3", className)} {...props}>
            <h6 className="m-0 fw-bold text-primary">
                {children}
            </h6>
        </CardHeader>
    );

    public static Body: React.FC<ICardBodyProps> = ({ children, ...props }) => (
        <CardBody {...props}>
            {children}
        </CardBody>
    );

    constructor(props: Readonly<ICardProps>) {
        super(props);

        this.state = {

        };
    }

    render() {
        const { className, children, ...props } = this.props;

        return (
            <ReactstrapCard className={classNames("shadow mb-4", className)} {...props}>
                {children}
            </ReactstrapCard>
        );
    }
}
