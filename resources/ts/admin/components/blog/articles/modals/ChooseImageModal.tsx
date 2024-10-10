import React from 'react';
import { Button, Col, FormGroup, Input, Label, Modal, ModalBody, ModalFooter, ModalHeader, Row } from 'reactstrap';

import Alerts from '@admin/components/alerts/Alerts';
import DragDropFile from '@admin/components/DragDropFile';

import { IPromptModalProps } from '@admin/utils/modals';
import { createBase64UrlFromFile } from '@admin/utils';

interface IChooseImageResult {
    file: File;
    /**
     * Base64 representation of image file
     *
     * @type {string}
     * @memberof IChooseImageResult
     */
    content: string;
    description: string;
}

interface ISelected {
    file: File;
    content: string;
}

const ChooseImageModal: React.FC<IPromptModalProps<IChooseImageResult>> = ({ onSuccess, onCancelled }) => {
    const [selected, setSelected] = React.useState<ISelected | undefined>(undefined);
    const [description, setDescription] = React.useState('');
    const [alerts, setAlerts] = React.useState<IAlert[]>([]);

    const isImageMimeTypeValid = React.useCallback(async (file: File): Promise<void> => new Promise((resolve, reject) => {
        if (!file.type.startsWith('image/')) {
            reject(`Image type "${file.type}" is not image.`);
            return;
        }

        resolve();
    }), []);

    const isImageContentValid = React.useCallback(async (file: File): Promise<string> => new Promise((resolve, reject) => {
        const img = new Image();

        img.addEventListener('load', () => {
            if (img.src)
                resolve(img.src);
            else
                reject('The image content is empty.');
        });

        img.addEventListener('error', (ev) => reject(`Image could not be loaded: ${ev.message}`));

        createBase64UrlFromFile(file).then((src) => {
            img.src = src;
        }).catch((reason) => reject(reason));
    }), []);

    const handleFileSelected = React.useCallback(async (file: File) => {
        try {
            await isImageMimeTypeValid(file);
            const content = await isImageContentValid(file);

            setSelected({ file, content });
        } catch (err) {
            logger.error(err);

            setAlerts([{ message: 'Please ensure the selected file is a valid image.', type: 'danger' }]);
        }
    }, []);

    const handleFileRemoved = React.useCallback(() => setSelected(undefined), []);

    const handleSelectClicked = React.useCallback(() => {
        if (!selected) {
            logger.error('Nothing is selected.');
            return;
        }

        onSuccess({ file: selected.file, content: selected.content, description });
    }, [selected, description, onSuccess]);

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
                            <DragDropFile
                                multiple={false}
                                accept="image/*"
                                onFileSelected={handleFileSelected}
                                onFileRemoved={handleFileRemoved}
                            >
                                {selected && (
                                    <img src={selected.content} alt='Main image' className='img-fluid' />
                                )}
                            </DragDropFile>
                        </Col>

                        <Col xs={12}>
                            <FormGroup floating className='mt-3'>
                                <Input
                                    name="description"
                                    id="description"
                                    placeholder="Description (optional)"
                                    type="text"
                                    value={description}
                                    onChange={(e) => setDescription(e.target.value)}
                                    onBlur={(e) => setDescription(e.target.value)}
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

export default ChooseImageModal;
