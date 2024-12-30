import React from 'react';
import { Button, Col, Modal, ModalBody, ModalFooter, ModalHeader, Row } from 'reactstrap';

import axios from 'axios';

import DragDropFile from '@admin/components/DragDropFile';
import Avatar from '@admin/components/avatar/Avatar';
import Alerts from '@admin/components/alerts/Alerts';

import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import { createAuthRequest } from '@admin/utils/api/factories';
import { createBase64UrlFromFile } from '@admin/utils';
import { IPromptModalProps } from '@admin/utils/modals';

export interface IAvatarUploaded {
    file: File;
    src: string;
}

const UploadAvatarModal: React.FC<IPromptModalProps> = ({ onSuccess, onCancelled }) => {
    const [selected, setSelected] = React.useState<IAvatarUploaded | undefined>(undefined);
    const [alerts, setAlerts] = React.useState<IAlert[]>([]);

    const uploadAvatar = React.useCallback(async (avatar: IAvatarUploaded) => {
        try {
            const data = new FormData();

            data.append('avatar', avatar.file);

            await createAuthRequest().post<IMessageResponse>('user/avatar', data, { headers: { 'Content-Type': 'multipart/form-data' } });

            onSuccess();
        } catch (err) {
            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            setAlerts([...alerts, { type: 'danger', message }]);

        }
    }, [onSuccess]);

    const onFileSelected = React.useCallback(async (file: File) => {
        const src = await createBase64UrlFromFile(file);

        setSelected({ file, src });
    }, []);

    const onFileRemoved = React.useCallback(() => setSelected(undefined), []);

    const handleUploadClicked = React.useCallback((e: React.MouseEvent) => {
        e.preventDefault();

        if (!selected) {
            logger.error('Upload was clicked when no avatar is selected.');
            return;
        }

        uploadAvatar(selected);
    }, [selected, uploadAvatar]);

    React.useEffect(() => {
        return () => {
            setSelected(undefined);
            setAlerts([]);
        };
    }, []);

    return (
        <>
            <Modal isOpen={true} toggle={onCancelled}>
                <ModalHeader>
                    Upload Avatar
                </ModalHeader>
                <ModalBody>
                    <Row>
                        <Col>
                            <Alerts alerts={alerts} />
                        </Col>
                    </Row>
                    <DragDropFile multiple={false} accept="image/*" onFileSelected={onFileSelected} onFileRemoved={onFileRemoved}>
                        {selected && <Avatar src={selected.src} alt='Avatar to upload' style={{ maxWidth: '100%' }} />}
                    </DragDropFile>
                </ModalBody>
                <ModalFooter>
                    <Button color="primary" onClick={handleUploadClicked} disabled={selected === undefined}>
                        Upload
                    </Button>
                    {' '}
                    <Button onClick={onCancelled}>
                        Cancel
                    </Button>
                </ModalFooter>
            </Modal>
        </>
    );
}

export default UploadAvatarModal;
