import React from "react";
import { Card, CardBody, Col, Row } from "reactstrap";
import { FaComments } from "react-icons/fa";

interface IProps {
    comments?: number;
}

const CommentsCard: React.FC<IProps> = ({ comments }) => {
    return (
        <Card className="border-left-info shadow h-100 py-2">
            <CardBody>
                <Row className="no-gutters align-items-center">
                    <Col className="me-2">
                        <div className="fw-bold text-info text-uppercase mb-1">
                            Comments
                        </div>
                        <div className="h5 mb-0 fw-bold text-gray-800">{comments ?? 'N/A'}</div>
                    </Col>
                    <Col xs="auto">
                        <FaComments className="fa-2x text-gray-300" />
                    </Col>
                </Row>
            </CardBody>
        </Card>
    );
}

export default CommentsCard;
