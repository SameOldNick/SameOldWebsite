import React from 'react';
import { Button, Card, CardBody, CardProps, CardTitle, Col, Row } from 'reactstrap';
import { FaFileImage, FaTrash } from 'react-icons/fa';

import ImageDisplay, { ICurrentImage } from './controls/main-image/ImageDisplay';

import ArticleEditorContext from '@admin/components/blog/articles/editor/ArticleEditorContext';

interface MainImageInputs {
    mainImage?: ICurrentImage;
    onMainImageSelected: () => Promise<void>;
    onMainImageRemoved: () => Promise<void>;
}

type TMainImageProps = Omit<CardProps, 'children'>;

const MainImage: React.FC<TMainImageProps> = ({ ...props }) => {
    const { inputs: { mainImage, onMainImageSelected, onMainImageRemoved } } = React.useContext(ArticleEditorContext);

    return (
        <>
            <Card {...props}>
                <CardBody>
                    <CardTitle tag='h5'>Main Image</CardTitle>

                    <ImageDisplay current={mainImage} />

                    <Row className='mt-3'>
                        <Col style={{ textAlign: 'center' }}>
                            <Button color='primary' size='md' className='me-3' onClick={() => onMainImageSelected()}>
                                <span className='me-1'>
                                    <FaFileImage />
                                </span>
                                Choose...
                            </Button>
                            <Button color='danger' size='md' disabled={mainImage === undefined} onClick={() => onMainImageRemoved()}>
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
export { TMainImageProps, MainImageInputs };
