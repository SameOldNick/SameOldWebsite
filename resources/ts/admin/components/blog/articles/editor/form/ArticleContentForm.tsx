import React from 'react';
import { Card, CardBody, CardProps, Col, Row } from 'reactstrap';

import Slug from './controls/article-form/Slug';
import Content from './controls/article-form/Content';
import Title from './controls/article-form/Title';
import Summary from './controls/article-form/Summary';

type ArticleContentFormProps = Omit<CardProps, 'children'>;

const ArticleContentForm: React.FC<ArticleContentFormProps> = ({ ...props }) => {
    return (
        <>
            <Card {...props}>
                <CardBody>
                    <Row>
                        <Col md={7}>
                            <Title />
                        </Col>
                        <Col md={5}>
                            <Slug />
                        </Col>
                    </Row>

                    <Row>
                        <Col xs={12}>
                            <Content />
                        </Col>

                        <Col xs={12}>
                            <Summary />
                        </Col>
                    </Row>
                </CardBody>
            </Card>
        </>
    );
}

export default ArticleContentForm;
export { ArticleContentFormProps };
