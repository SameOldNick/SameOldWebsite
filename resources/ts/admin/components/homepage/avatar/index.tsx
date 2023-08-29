import React from 'react';
import { Button, Col, Row } from 'reactstrap';
import withReactContent from 'sweetalert2-react-content';
import { FaTrash, FaUpload } from 'react-icons/fa';

import Swal from 'sweetalert2';

import Upload from './UploadAvatarModal';
import Remove from './RemoveAvatarModal';

import AvatarImage from '@admin/components/Avatar';
import CurrentAvatar from '@admin/components/hoc/CurrentAvatar';

interface IProps {
}

interface IState {
    uploadModal: boolean;
    removeModal: boolean;
}

export default class Avatar extends React.Component<IProps, IState> {
    private readonly _avatarRef: React.RefObject<AvatarImage>;

    constructor(props: Readonly<IProps>) {
        super(props);

        this._avatarRef = React.createRef();

        this.state = {
            uploadModal: false,
            removeModal: false
        };

        this.onUploaded = this.onUploaded.bind(this);
        this.onRemoved = this.onRemoved.bind(this);
    }

    private async onUploaded() {
        this.setStateAndResolve({ uploadModal: false });

        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'Avatar Updated Successfully'
        });

        this._avatarRef.current?.refresh();
    }

    private async onRemoved() {
        this.setStateAndResolve({ removeModal: false });

        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'Avatar Removed Successfully'
        });

        this._avatarRef.current?.refresh();
    }

    public render() {
        const { uploadModal, removeModal } = this.state;

        return (
            <>
                <Row className='mb-3'>
                    <Col style={{ textAlign: 'center' }}>
                        <CurrentAvatar ref={this._avatarRef} size={125} />
                    </Col>
                </Row>
                <Row>
                    <Col style={{ textAlign: 'center' }}>
                        <Button color='primary' size='md' className='me-3' onClick={() => this.setState({ uploadModal: true })}>
                            <span className='me-1'>
                                <FaUpload />
                            </span>
                            Upload...
                        </Button>
                        <Button color='danger' size='md' onClick={() => this.setState({ removeModal: true })}>
                            <span className='me-1'>
                                <FaTrash />
                            </span>
                            Remove
                        </Button>
                    </Col>
                </Row>

                {uploadModal && <Upload onUploaded={this.onUploaded} onCancelled={() => this.setState({ uploadModal: false })} />}
                {removeModal && <Remove onRemoved={this.onRemoved} onCancelled={() => this.setState({ removeModal: false })} />}
            </>
        );
    }
}
