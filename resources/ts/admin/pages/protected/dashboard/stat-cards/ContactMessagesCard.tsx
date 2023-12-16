import React from "react";
import { FaEnvelope } from "react-icons/fa";
import { Card, CardBody, Col, Row } from "reactstrap";

interface IProps {
    messages?: number;
}

const ContactMessagesCard: React.FC<IProps> = ({ messages }) => {
    return (
        <Card className="border-left-warning shadow h-100 py-2">
            <CardBody>
                <Row className="no-gutters align-items-center">
                    <Col className="me-2">
                        <div className="fw-bold text-warning text-uppercase mb-1">
                            Contact Messages
                        </div>
                        <div className="h5 mb-0 fw-bold text-gray-800">{messages ?? 'N/A'}</div>
                    </Col>
                    <Col xs="auto">
                        <FaEnvelope className="fa-2x text-gray-300" />
                    </Col>
                </Row>
            </CardBody>
        </Card>
    );
}

export default ContactMessagesCard;
