import React from 'react';
import { Button, Col, FormGroup, Input, Label, Modal, ModalBody, ModalFooter, ModalHeader, Row } from 'reactstrap';

import S from 'string';
import { DateTime } from 'luxon';

import Article from '@admin/utils/api/models/Article';

interface IArticleInfoModalProps {
    article: Article;
    onClosed: () => void;
}

const ArticleInfoModal: React.FC<IArticleInfoModalProps> = ({ article, onClosed }) => {
    const handleClosed = () => {
        onClosed();
    }

    return (
        <>
            <Modal isOpen={true} toggle={handleClosed}>
                <ModalHeader>
                    Article Info
                </ModalHeader>
                <ModalBody>
                    <Row>

                        <Col xs={12}>
                            <FormGroup row>
                                <Label for='status' sm={3} className='text-end'>
                                    Status:
                                </Label>
                                <Col sm={9}>
                                    <Input
                                        id="status"
                                        name="status"
                                        type="text"
                                        readOnly
                                        value={S(article.status).capitalize().s}
                                    />
                                </Col>
                            </FormGroup>
                            <FormGroup row>
                                <Label for="published" sm={3} className='text-end'>
                                    Published:
                                </Label>
                                <Col sm={9}>
                                    <Input
                                        id="published"
                                        name="published"
                                        type="text"
                                        readOnly
                                        value={`${article.publishedAt && article.publishedAt.toRelative() || 'N/A'} (${article.publishedAt?.toLocaleString(DateTime.DATETIME_SHORT_WITH_SECONDS)})`}
                                    />
                                </Col>
                            </FormGroup>
                            <FormGroup row>
                                <Label for="saved" sm={3} className='text-end'>
                                    Last Saved:
                                </Label>
                                <Col sm={9}>
                                    <Input
                                        id="saved"
                                        name="saved"
                                        type="text"
                                        readOnly
                                        value={`${article.createdAt && article.createdAt.toRelative() || 'N/A'} (${article.createdAt?.toLocaleString(DateTime.DATETIME_SHORT_WITH_SECONDS)})`}

                                    />
                                </Col>
                            </FormGroup>
                        </Col>
                    </Row>
                </ModalBody>
                <ModalFooter>
                    <Button color='primary' onClick={handleClosed}>
                        Close
                    </Button>
                </ModalFooter>
            </Modal>
        </>
    );
}

export default ArticleInfoModal;
