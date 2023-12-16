import React from "react";
import { FaNewspaper } from "react-icons/fa";
import { Card, CardBody, Col, Row } from "reactstrap";

interface IProps {
    articles?: number;
}

const BlogArticlesCard: React.FC<IProps> = ({ articles }) => {
    return (
        <Card className='border-left-success shadow h-100 py-2'>
            <CardBody>
                <Row className='no-gutters align-items-center'>
                    <Col className='me-2'>
                        <div className="fw-bold text-success text-uppercase mb-1">
                            Blog Articles
                        </div>
                        <div className="h5 mb-0 fw-bold text-gray-800">{articles ?? 'N/A'}</div>
                    </Col>
                    <Col xs='auto'>
                        <FaNewspaper className='fa-2x text-gray-300' />
                    </Col>
                </Row>
            </CardBody>
        </Card>
    );
}

export default BlogArticlesCard;
