import React from 'react';
import { Button, Col, Row } from 'reactstrap';
import withReactContent from 'sweetalert2-react-content';
import { FaTrash, FaUpload } from 'react-icons/fa';

import Swal from 'sweetalert2';

import Upload from './UploadAvatarModal';
import Remove from './RemoveAvatarModal';

import awaitModalPrompt from '@admin/utils/modals';
import CurrentUserAvatar from '@admin/components/avatar/CurrentUserAvatar';

const Avatar: React.FC = () => {
    const [avatarKey, setAvatarKey] = React.useState(0);

    const handleUploadClicked = React.useCallback(async () => {
        try {
            await awaitModalPrompt(Upload);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Avatar Updated Successfully'
            });

            setAvatarKey((avatarKey) => avatarKey + 1);
        } catch (err) {
            // User cancelled modal
            logger.error(err);
        }
    }, []);

    const handleRemoveClicked = React.useCallback(async () => {
        try {
            await awaitModalPrompt(Remove);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Avatar Removed Successfully'
            });

            setAvatarKey((avatarKey) => avatarKey + 1);
        } catch (err) {
            // User cancelled modal
            logger.error(err);
        }
    }, []);

    return (
        <>
            <Row className='mb-3'>
                <Col style={{ textAlign: 'center' }}>
                    <CurrentUserAvatar key={avatarKey} className='img-fluid' />
                </Col>
            </Row>
            <Row>
                <Col style={{ textAlign: 'center' }}>
                    <Button color='primary' size='md' className='me-3' onClick={handleUploadClicked}>
                        <span className='me-1'>
                            <FaUpload />
                        </span>
                        Upload...
                    </Button>
                    <Button color='danger' size='md' onClick={handleRemoveClicked}>
                        <span className='me-1'>
                            <FaTrash />
                        </span>
                        Remove
                    </Button>
                </Col>
            </Row>
        </>
    );
}

export default Avatar;
