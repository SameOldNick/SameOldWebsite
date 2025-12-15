import React from 'react';
import { Badge, Button, Col, FormGroup, Input, Label, Modal, ModalBody, ModalFooter, ModalHeader, Row } from 'reactstrap';
import { FaDownload } from 'react-icons/fa';
import withReactContent from 'sweetalert2-react-content';

import S from 'string';
import { DateTime } from 'luxon';
import axios from 'axios';
import Swal from 'sweetalert2';

import Backup from '@admin/utils/api/models/Backup';
import { IPromptModalProps } from '@admin/utils/modals';
import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';

interface IBackupModalProps extends IPromptModalProps {
    backup: Backup;
}

const BackupInfoModal: React.FC<IBackupModalProps> = ({ backup, onSuccess }) => {
    const [loading, setLoading] = React.useState(false);

    const handleClosed = React.useCallback(() => {
        onSuccess();
    }, [onSuccess]);

    const handleDownloadClicked = async (e: React.MouseEvent) => {
        e.preventDefault();

        setLoading(true);

        try {
            const response = await createAuthRequest().get<Record<'url', string>>(`backups/${backup.backup.uuid}/download`);

            window.open(response.data.url, '_blank');
        } catch (err) {
            logger.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `Unable to generate donwload link: ${message}`,
                confirmButtonText: 'Try Again',
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed) {
                await handleDownloadClicked(e);
            }
        } finally {
            setLoading(false);
        }
    }

    return (
        <>
            <Modal isOpen={true} backdrop='static' size='lg' toggle={handleClosed}>
                <ModalHeader>
                    Backup
                </ModalHeader>
                <ModalBody>
                    <Row>

                        <Col xs={12}>
                            <FormGroup row>
                                <Label for='status' sm={3} className='text-end'>
                                    Status:
                                </Label>
                                <Col sm={9} className='align-content-center'>
                                    <Badge color={backup.status === 'successful' ? 'success' : 'danger'}>
                                        {S(backup.status).humanize().s}
                                    </Badge>
                                </Col>
                            </FormGroup>

                            {backup.errorMessage && (
                                <FormGroup row>
                                    <Label for='message' sm={3} className='text-end'>
                                        Error message:
                                    </Label>
                                    <Col sm={9}>
                                        <Input
                                            id="message"
                                            name="message"
                                            type="textarea"
                                            rows={6}
                                            readOnly
                                            value={backup.backup.error_message}
                                        />
                                    </Col>
                                </FormGroup>
                            )}

                            {backup.file && (
                                <>
                                    <FormGroup row>
                                        <Label for="filename" sm={3} className='text-end'>
                                            Filename:
                                        </Label>
                                        <Col sm={9}>
                                            <Input
                                                id="filename"
                                                name="filename"
                                                type="text"
                                                readOnly
                                                value={backup.file.name}
                                            />
                                        </Col>
                                    </FormGroup>
                                </>
                            )}


                            <FormGroup row>
                                <Label for="created" sm={3} className='text-end'>
                                    Created:
                                </Label>
                                <Col sm={9}>
                                    <Input
                                        id="created"
                                        name="created"
                                        type="text"
                                        readOnly
                                        value={backup.createdAt.toLocaleString(DateTime.DATETIME_SHORT_WITH_SECONDS)}
                                    />
                                </Col>
                            </FormGroup>
                        </Col>
                    </Row>
                </ModalBody>
                <ModalFooter>
                    {backup.file && (
                        <Button color='success' onClick={handleDownloadClicked} disabled={loading}>
                            <span className="me-1">
                                <FaDownload />
                            </span>
                            Download
                        </Button>
                    )}

                    <Button color='primary' onClick={handleClosed}>
                        Close
                    </Button>
                </ModalFooter>
            </Modal>
        </>
    );
}

export default BackupInfoModal;
