import React from 'react';
import { Button, Col, Row } from 'reactstrap';
import { FaEdit, FaSync, FaTimesCircle, FaTrash } from 'react-icons/fa';
import withReactContent from 'sweetalert2-react-content';

import Swal from 'sweetalert2';
import axios from 'axios';
import classNames from 'classnames';

import Icon from '@admin/components/icon-selector/Icon';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import { lookupIcon } from '@admin/components/icon-selector/utils';
import TechnologyPrompt from './TechnologyPrompt';

interface IProps {

}

interface ITechnologyProps {
    technology: ITechnology;
    selected: boolean;

    onEditClicked: () => void;
    onDeleteClicked: () => void;
    onSelected: (selected: boolean) => void;
}

interface ITechnologyItem {
    technology: ITechnology;
    selected: boolean;
}

interface IState {
    addTechnology: boolean;
    editTechnology?: ITechnology;
    technologies: ITechnologyItem[];
}

export default class TechnologyList extends React.Component<IProps, IState> {
    static Technology: React.FC<ITechnologyProps> = ({ technology, selected, onSelected, onEditClicked, onDeleteClicked }) => {
        const icon = React.useMemo(() => lookupIcon(technology.icon), [technology]);

        const handleClick = (e: React.MouseEvent<HTMLElement>) => {
            onSelected(!selected)
        }

        const handleEditClick = (e: React.MouseEvent<HTMLButtonElement>) => {
            e.stopPropagation();
            onEditClicked();
        }

        const handleDeleteClick = (e: React.MouseEvent<HTMLButtonElement>) => {
            e.stopPropagation();
            onDeleteClicked();
        }

        return (
            <Col
                xs={3}
                className={classNames('border rounded p-3', { 'bg-body-secondary': selected })}
                onClick={handleClick}
                style={{ cursor: 'pointer' }}
            >
                <div className='d-flex justify-content-center'>
                    <div
                        className={classNames('d-flex justify-content-center align-items-center rounded-circle')}
                        style={{ backgroundColor: '#8cbb45', width: 100, height: 100 }}
                    >
                        {icon && <Icon icon={icon} size={45} />}
                    </div>
                </div>
                <h2 className={classNames('text-center')}>
                    {technology.technology}
                </h2>
                <div className='d-flex justify-content-center'>
                    <Button color="primary" className='align-middle me-1' onClick={handleEditClick}>
                        <span className='me-1'>
                            <FaEdit />
                        </span>
                        Edit
                    </Button>
                    <Button color="danger" className='align-middle' onClick={handleDeleteClick}>
                        <span className='me-1'>
                            <FaTimesCircle />
                        </span>
                        Delete
                    </Button>
                </div>
            </Col>
        );
    }

    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
            addTechnology: false,
            technologies: []
        };

        this.load = this.load.bind(this);
        this.addTechnology = this.addTechnology.bind(this);
        this.editTechnology = this.editTechnology.bind(this);
        this.deleteTechnology = this.deleteTechnology.bind(this);
        this.deleteTechnologies = this.deleteTechnologies.bind(this);
        this.promptDeleteTechnology = this.promptDeleteTechnology.bind(this);
        this.onItemSelected = this.onItemSelected.bind(this);
        this.displayEditTechnology = this.displayEditTechnology.bind(this);
    }

    componentDidMount() {
        this.load();
    }

    private async load() {
        try {
            const response = await createAuthRequest().get<ITechnology[]>('technologies');

            this.setState({ technologies: response.data.map((technology) => ({ technology, selected: false })) });
        } catch (err) {
            console.error(err);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `An error occurred trying to load technologies.`,
                confirmButtonText: 'Try Again',
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed)
                await this.load();
        }
    }

    private async addTechnology(newTechnology: ITechnology) {
        try {
            const response = await createAuthRequest().post('technologies', newTechnology);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Technology has been added.'
            });

            await this.load();
        } catch (err) {
            console.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `Unable to add technology: ${message}`,
                confirmButtonText: 'Try Again',
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed)
                await this.addTechnology(newTechnology);
        }
    }

    private async editTechnology(technology: ITechnology) {
        try {
            const response = await createAuthRequest().put(`technologies/${technology.id}`, technology);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Technology has been updated.'
            });

            await this.load();
        } catch (err) {
            console.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `Unable to edit technology: ${message}`,
                confirmButtonText: 'Try Again',
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed)
                await this.editTechnology(technology);
        }
    }

    private async promptDeleteTechnology(technology: ITechnology) {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Do you really want to remove "${technology.technology}"?`,
            showConfirmButton: true,
            showCancelButton: true
        });

        if (!result.isConfirmed)
            return;

        const data = await this.deleteTechnology(technology);

        if (data !== false) {
            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: data.success
            });
        }

        await this.load();
    }

    private async deleteTechnology(technology: ITechnology): Promise<Record<'success', string> | false> {
        try {
            const response = await createAuthRequest().delete<Record<'success', string>>(`technologies/${technology.id}`);

            return response.data;
        } catch (err) {
            console.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `Unable to delete technology: ${message}`,
                confirmButtonText: 'Try Again',
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed)
                return await this.deleteTechnology(technology);
            else
                return false;
        }
    }

    private async deleteTechnologies() {
        const { technologies } = this.state;

        const toDelete = technologies.filter((value) => value.selected);

        if (toDelete.length === 0) {
            await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `No technologies are selected.`
            });

            return;
        }

        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Do you really want to remove ${toDelete.length} technologies?`,
            showConfirmButton: true,
            showCancelButton: true
        });

        if (!result.isConfirmed)
            return;

        await Promise.all(toDelete.map(({ technology }) => this.deleteTechnology(technology)));

        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'Success!',
            text: `Deleted ${toDelete.length} technologies.`
        });

        await this.load();
    }


    private onItemSelected(technology: ITechnology, selected: boolean) {
        this.setState(({ technologies }) => ({ technologies: technologies.map((item) => item.technology === technology ? { technology, selected } : item) }));
    }

    private displayEditTechnology(technology: ITechnology) {
        this.setState({ editTechnology: technology });
    }

    public render() {
        const { } = this.props;
        const { addTechnology, editTechnology, technologies } = this.state;

        const hasSelected = () => {
            for (const { selected } of technologies) {
                if (selected)
                    return true;
            }

            return false;
        }

        return (
            <>

                {addTechnology && (
                    <TechnologyPrompt
                        onSubmitted={this.addTechnology}
                        onClose={() => this.setState({ addTechnology: false })}
                    />
                )}

                {editTechnology && (
                    <TechnologyPrompt
                        technology={editTechnology}
                        onSubmitted={this.editTechnology}
                        onClose={() => this.setState({ editTechnology: undefined })}
                    />
                )}

                <Row className="mb-3">
                    <Col className="d-flex justify-content-between">
                        <div>
                            <Button color='primary' onClick={() => this.setState({ addTechnology: true })}>Add Technology</Button>
                        </div>

                        <div>
                            <Button color='primary' className="me-1" onClick={this.load}>
                                <span className='me-1'>
                                    <FaSync />
                                </span>
                                Update
                            </Button>

                            <Button color="danger" disabled={!hasSelected()} onClick={this.deleteTechnologies}>
                                <span className='me-1'>
                                    <FaTrash />
                                </span>
                                Delete Selected
                            </Button>
                        </div>
                    </Col>
                </Row>

                <Row className='mx-1 gap-2 justify-content-center'>
                    {technologies.length > 0 && technologies.map(({ technology, selected }, index) => (
                        <TechnologyList.Technology
                            key={index}
                            technology={technology}
                            selected={selected}
                            onSelected={(selected) => this.onItemSelected(technology, selected)}
                            onEditClicked={() => this.displayEditTechnology(technology)}
                            onDeleteClicked={() => this.promptDeleteTechnology(technology)}
                        />
                    ))}
                    {technologies.length === 0 && <div>No technologies found.</div>}
                </Row>
            </>
        );
    }
}
