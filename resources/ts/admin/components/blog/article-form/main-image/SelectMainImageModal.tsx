import React from 'react';
import { Button, Col, FormGroup, Input, Label, Modal, ModalBody, ModalFooter, ModalHeader, Row } from 'reactstrap';

import DragDropFile from '@admin/components/DragDropFile';
import Alerts, { IAlert } from '@admin/components/Alerts';

import { createBase64UrlFromFile } from '@admin/utils';

export interface IUploaded {
    file: File;
    src: string;
}

export interface ISelected {
    uploaded: IUploaded;
    description: string;
}

interface IProps {
    onSelected: (selected: ISelected) => void;
    onCancelled: () => void;
}

interface IState {
    uploaded?: IUploaded;
}

const SelectMainImageModal: React.FC<IProps> = ({ onSelected, onCancelled }) => {
    const descriptionRef = React.createRef<HTMLInputElement>();

    const [uploaded, setUploaded] = React.useState<IUploaded | undefined>(undefined);
    const [alerts, setAlerts] = React.useState<IAlert[]>([]);

    const handleFileSelected = async (file: File) => {
        const src = await createBase64UrlFromFile(file);

        setUploaded({ file, src });
    }

    const handleFileRemoved = () => setUploaded(undefined);

    const handleSelectClicked = (e: React.MouseEvent) => {
        if (!uploaded)
            return;

        onSelected({
            uploaded,
            description: descriptionRef.current?.value || ''
        });
    }

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
                    Upload Main Image
                </ModalHeader>
                <ModalBody>
                    <Row>
                        <Col xs={12}>
                            <Alerts alerts={alerts} />
                        </Col>

                        <Col xs={12}>
                            <DragDropFile multiple={false} accept="image/*" onFileSelected={handleFileSelected} onFileRemoved={handleFileRemoved}>
                                {uploaded && <img src={uploaded.src} alt='Main image' className='img-fluid' />}
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
                    <Button color="primary" onClick={handleSelectClicked} disabled={uploaded === undefined}>
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

export default SelectMainImageModal;
