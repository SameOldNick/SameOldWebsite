import React from 'react';
import { Button, Col, FormGroup, Input, Label, Modal, ModalBody, ModalFooter, ModalHeader, Row } from 'reactstrap';

import axios from 'axios';

import DragDropFile from '@admin/components/DragDropFile';
import Alerts from '@admin/components/alerts/Alerts';

import { createBase64UrlFromFile } from '@admin/utils';
import { IPromptModalProps } from '@admin/utils/modals';
import { uploadImage } from '@admin/utils/api/endpoints/articles';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';

export interface ISelectedImage {
    file: File;
    src: string;
}

const UploadImageModal: React.FC<IPromptModalProps<IImage>> = ({ onSuccess, onCancelled }) => {
    const descriptionRef = React.createRef<HTMLInputElement>();

    const [selected, setSelected] = React.useState<ISelectedImage | undefined>(undefined);
    const [alerts, setAlerts] = React.useState<IAlert[]>([]);

    const handleFileSelected = React.useCallback(async (file: File) => {
        const src = await createBase64UrlFromFile(file);

        setSelected({ file, src });
    }, []);

    const handleFileRemoved = React.useCallback(() => setSelected(undefined), []);

    const handleSelectClicked = React.useCallback(async () => {
        if (!selected)
            return;

        try {
            const uploaded = await uploadImage(selected.file, descriptionRef.current?.value || '');

            onSuccess(uploaded);
        } catch (err) {
            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            setAlerts([...alerts, { type: 'danger', message }]);
        }
    }, [onSuccess]);

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
                    Upload Main Image
                </ModalHeader>
                <ModalBody>
                    <Row>
                        <Col xs={12}>
                            <Alerts alerts={alerts} />
                        </Col>

                        <Col xs={12}>
                            <DragDropFile multiple={false} accept="image/*" onFileSelected={handleFileSelected} onFileRemoved={handleFileRemoved}>
                                {selected && <img src={selected.src} alt='Main image' className='img-fluid' />}
                            </DragDropFile>
                        </Col>

                        <Col xs={12}>
                            <FormGroup floating className='mt-3'>
                                <Input
                                    innerRef={descriptionRef}
                                    name="description"
                                    id="description"
                                    placeholder="Description (optional)"
                                    type="text"
                                />
                                <Label for="description">
                                    Description (optional)
                                </Label>
                            </FormGroup>
                        </Col>
                    </Row>
                </ModalBody>
                <ModalFooter>
                    <Button color="primary" onClick={handleSelectClicked} disabled={selected === undefined}>
                        Select
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

export default UploadImageModal;
