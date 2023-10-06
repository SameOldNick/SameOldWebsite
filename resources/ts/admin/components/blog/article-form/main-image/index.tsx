import React from "react";
import { FaRegImage, FaTrash, FaUpload } from "react-icons/fa";
import { Button, Card, CardProps, CardBody, CardTitle, Col, Row } from "reactstrap";
import withReactContent from "sweetalert2-react-content";

import Swal from "sweetalert2";

import SelectMainImageModal, { ISelected } from "./SelectMainImageModal";

export interface IMainImageNew {
    file: File;
    src: string;
    description: string;
}

export interface IMainImageExisting {
    src: string;
    description: string;
}

export type TMainImage = IMainImageNew | IMainImageExisting;

export const isMainImageNew = (obj: any): obj is IMainImageNew =>
    typeof obj === 'object' && typeof obj.src === 'string' && typeof obj.description === 'string' && typeof obj.file === 'object' && typeof obj.file.name === 'string';

export const isMainImageExisting = (obj: any): obj is IMainImageNew =>
    typeof obj === 'object' && typeof obj.src === 'string' && typeof obj.description === 'string' && typeof obj.file === 'undefined';

interface IProps extends Omit<CardProps, 'children' | 'onChange'> {
    current?: TMainImage;
    onChange: (image?: TMainImage) => void;
}

const SelectMainImage: React.FC<IProps> = ({ current, onChange, ...props }) => {
    const [uploadModal, showUploadModal] = React.useState(false);

    const handleModalSelected = ({ uploaded, description }: ISelected) => {
        onChange({ ...uploaded, description });

        showUploadModal(false);
    }

    const handleDeleteClicked = async () => {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Remove Main Image?',
            showConfirmButton: true,
            confirmButtonText: 'Yes',
            confirmButtonColor: 'danger',
            showCancelButton: true,
            cancelButtonText: 'No',
            cancelButtonColor: 'primary',
        });

        if (result.isConfirmed) {
            onChange(undefined);
        }
    }

    return (
        <>
            {uploadModal && <SelectMainImageModal onSelected={handleModalSelected} onCancelled={() => showUploadModal(false)} />}

            <Card {...props}>
                <CardBody>
                    <CardTitle tag='h5' className='mb-0'>Main Image</CardTitle>

                </CardBody>

                {current === undefined && (
                    <div className='text-center'>
                        <FaRegImage size='200' />
                        <p className='mb-0 fw-bold'>No Image Selected</p>
                    </div>
                )}

                {current !== undefined && (
                    <div className='text-center'>
                        <img src={current.src} className='img-fluid mb-3' style={{ maxHeight: '250px' }} />
                        <p className='mb-0 fw-bold'>{current.description}</p>
                    </div>
                )}

                <CardBody>
                    <Row>
                        <Col style={{ textAlign: 'center' }}>
                            <Button color='primary' size='md' className='me-3' onClick={() => showUploadModal(true)}>
                                <span className='me-1'>
                                    <FaUpload />
                                </span>
                                Upload...
                            </Button>
                            <Button color='danger' size='md' onClick={handleDeleteClicked}>
                                <span className='me-1'>
                                    <FaTrash />
                                </span>
                                Remove
                            </Button>
                        </Col>
                    </Row>
                </CardBody>
            </Card>
        </>
    );
}

export default SelectMainImage;
