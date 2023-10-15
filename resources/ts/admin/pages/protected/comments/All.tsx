import React from 'react';
import { Helmet } from 'react-helmet';
import { Card, CardBody } from 'reactstrap';

import Heading from '@admin/layouts/admin/Heading';
import CommentList from '@admin/components/blog/comments/CommentList';

interface IProps {

}

const All: React.FC<IProps> = ({ }) => {
    return (
        <>
            <Helmet>
                <title>All Comments</title>
            </Helmet>

            <Heading title='All Comments' />

            <Card>
                <CardBody>
                    <CommentList />
                </CardBody>
            </Card>
        </>
    );
}

export default All;
