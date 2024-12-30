import React from 'react';
import {
    Card as ReactstrapCard,
    CardProps as ReactstrapCardProps,
    CardBody,
    CardBodyProps as ReactstrapCardBodyProps,
    CardHeader,
    CardHeaderProps as ReactstrapCardHeaderProps
} from 'reactstrap';

import classNames from 'classnames';

type CardProps = React.PropsWithChildren<ReactstrapCardProps>;
type CardHeaderProps = React.PropsWithChildren<ReactstrapCardBodyProps>;
type CardBodyProps = React.PropsWithChildren<ReactstrapCardHeaderProps>;

export default class Card extends React.Component<CardProps> {
    public static Header: React.FC<CardHeaderProps> = ({ className, children, ...props }) => (
        <CardHeader className={classNames("py-3", className)} {...props}>
            <h6 className="m-0 fw-bold text-primary">
                {children}
            </h6>
        </CardHeader>
    );

    public static Body: React.FC<CardBodyProps> = ({ children, ...props }) => (
        <CardBody {...props}>
            {children}
        </CardBody>
    );

    constructor(props: Readonly<CardProps>) {
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
