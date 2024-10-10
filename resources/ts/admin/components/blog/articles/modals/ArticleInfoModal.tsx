import React from 'react';
import { Button, Col, FormGroup, Input, Label, Modal, ModalBody, ModalFooter, ModalHeader, Row } from 'reactstrap';

import S from 'string';
import { DateTime } from 'luxon';

import Article from '@admin/utils/api/models/Article';
import { IPromptModalProps } from '@admin/utils/modals';

interface IArticleInfoModalProps extends IPromptModalProps {
    article: Article;
}

const ArticleInfoModal: React.FC<IArticleInfoModalProps> = ({ article, onSuccess }) => {
    const handleClosed = React.useCallback(() => {
        onSuccess();
    }, [onSuccess]);

    const generateDateTime = React.useCallback(
        (dateTime?: DateTime) =>
            dateTime ?
                `${dateTime.toRelative()} (${dateTime.toLocaleString(DateTime.DATETIME_SHORT_WITH_SECONDS)})` :
                'N/A',
        []);

    const publishedAt = React.useMemo(() => generateDateTime(article.publishedAt ?? undefined), [article]);
    const lastSavedAt = React.useMemo(() => generateDateTime(article.currentRevision?.createdAt ?? undefined), [article]);

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
                                        value={publishedAt}
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
                                        value={lastSavedAt}
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
