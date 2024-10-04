import Image from "@admin/utils/api/models/Image";
import React from "react";
import { FaRegImage, FaTrash, FaUpload } from "react-icons/fa";
import { Button, Card, CardProps, CardBody, CardTitle, Col, Row } from "reactstrap";

export interface IMainImage {
    src: string;
    description: string;
}

interface IProps extends Omit<CardProps, 'children' | 'onChange'> {
    current?: Image;
    onUploadClicked: () => Promise<void>;
    onRemoveClicked: () => Promise<void>;
}

const MainImage: React.FC<IProps> = ({ current, onUploadClicked, onRemoveClicked, ...props }) => {
    const [disableButtons, setDisableButtons] = React.useState(false);

    const handleUploadButtonClicked = React.useCallback(async (e: React.MouseEvent<HTMLButtonElement>) => {
        e.preventDefault();

        try {
            setDisableButtons(true);

            await onUploadClicked();
        } finally {
            setDisableButtons(false);
        }
    }, [setDisableButtons, onUploadClicked]);

    const handleRemoveButtonClicked = React.useCallback(async (e: React.MouseEvent<HTMLButtonElement>) => {
        e.preventDefault();

        try {
            setDisableButtons(true);

            await onRemoveClicked();
        } finally {
            setDisableButtons(false);
        }
    }, [setDisableButtons, onRemoveClicked]);

    return (
        <>
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
                        <img src={current.url} className='img-fluid mb-3' style={{ maxHeight: '250px' }} />
                        <p className='mb-0 fw-bold'>{current.image.description}</p>
                    </div>
                )}

                <CardBody>
                    <Row>
                        <Col style={{ textAlign: 'center' }}>
                            <Button color='primary' size='md' className='me-3' disabled={disableButtons} onClick={handleUploadButtonClicked}>
                                <span className='me-1'>
                                    <FaUpload />
                                </span>
                                Upload...
                            </Button>
                            <Button color='danger' size='md' disabled={disableButtons} onClick={handleRemoveButtonClicked}>
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

export default MainImage;
