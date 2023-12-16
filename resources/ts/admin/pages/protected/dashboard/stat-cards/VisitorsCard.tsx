import React from "react";
import { Card, CardBody, Col, Row } from "reactstrap";

import { FaUsers } from "react-icons/fa";

interface IProps {
    count?: number;
}

const VisitorsCard: React.FC<IProps> = ({ count }) => {
    return (
        <Card className='border-left-primary shadow h-100 py-2'>
            <CardBody>
                <Row className='no-gutters align-items-center'>
                    <Col className='me-2'>
                        <div className="fw-bold text-primary text-uppercase mb-1">
                            Visitors (Last Month)
                        </div>
                        <div className="h5 mb-0 fw-bold text-gray-800">{count ?? 'N/A'}</div>
                    </Col>
                    <Col xs='auto'>
                        <FaUsers className='fa-2x text-gray-300' />
                    </Col>
                </Row>
            </CardBody>
        </Card>
    );
}

export default VisitorsCard;
