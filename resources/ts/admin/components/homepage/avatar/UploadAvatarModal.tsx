import React from 'react';
import { Button, Col, Modal, ModalBody, ModalFooter, ModalHeader, Row } from 'reactstrap';

import axios from 'axios';

import DragDropFile from '@admin/components/DragDropFile';
import Avatar from '@admin/components/Avatar';
import Alerts, { IAlert } from '@admin/components/Alerts';

import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import { createAuthRequest } from '@admin/utils/api/factories';
import { createBase64UrlFromFile } from '@admin/utils';

export interface IAvatarUploaded {
    file: File;
    src: string;
}

interface IProps {
    onUploaded: () => void;
    onCancelled: () => void;
}

interface IState {
    uploaded?: IAvatarUploaded;
}

const UploadAvatarModal: React.FC<IProps> = ({ onUploaded, onCancelled }) => {
    const [uploaded, setUploaded] = React.useState<IAvatarUploaded | undefined>(undefined);
    const [alerts, setAlerts] = React.useState<IAlert[]>([]);

    const uploadAvatar = async () => {
        if (uploaded === undefined)
            return;

        try {
            const data = new FormData();

            data.append('avatar', uploaded.file);

            const response = await createAuthRequest().post<IMessageResponse>('user/avatar', data, { headers: { 'Content-Type': 'multipart/form-data' } });

            onUploaded();
        } catch (err) {
            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            setAlerts([...alerts, { type: 'danger', message }]);

        }
    }

    const onFileSelected = async (file: File) => {
        const src = await createBase64UrlFromFile(file);

        setUploaded({ file, src });
    }

    const onFileRemoved = () => setUploaded(undefined);

    React.useEffect(() => {
        return () => {
            setUploaded(undefined);
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
                        {uploaded && <Avatar src={uploaded.src} alt='Avatar to upload' style={{ maxWidth: '100%' }} />}
                    </DragDropFile>
                </ModalBody>
                <ModalFooter>
                    <Button color="primary" onClick={() => uploadAvatar()} disabled={uploaded === undefined}>
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
