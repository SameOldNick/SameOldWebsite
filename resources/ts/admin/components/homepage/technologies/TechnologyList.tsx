import React from 'react';
import { Button, Col, Row } from 'reactstrap';
import { FaEdit, FaSync, FaTimesCircle, FaTrash } from 'react-icons/fa';
import withReactContent from 'sweetalert2-react-content';

import Swal from 'sweetalert2';
import axios from 'axios';
import classNames from 'classnames';

import Icon from '@admin/components/icon-selector/Icon';
import TechnologyPrompt from './TechnologyPrompt';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import { lookupIcon } from '@admin/components/icon-selector/utils';
import awaitModalPrompt from '@admin/utils/modals';

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

const Technology: React.FC<ITechnologyProps> = ({ technology, selected, onSelected, onEditClicked, onDeleteClicked }) => {
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

const TechnologyList: React.FC<IProps> = ({ }) => {
    const [renderCount, setRenderCount] = React.useState(1);
    const [technologies, setTechnologies] = React.useState<ITechnologyItem[]>([]);

    const load = async () => {
        try {
            const response = await createAuthRequest().get<ITechnology[]>('technologies');

            setTechnologies(response.data.map((technology) => ({ technology, selected: false })));
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
                await load();
        }
    }

    const addTechnology = async (newTechnology: ITechnology) => {
        try {
            const response = await createAuthRequest().post('technologies', newTechnology);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Technology has been added.'
            });

            await load();
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
                await addTechnology(newTechnology);
        }
    }

    const editTechnology = async (technology: ITechnology) => {
        try {
            const response = await createAuthRequest().put(`technologies/${technology.id}`, technology);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Technology has been updated.'
            });

            await load();
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
                await editTechnology(technology);
        }
    }

    const promptDeleteTechnology = async (technology: ITechnology) => {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Do you really want to remove "${technology.technology}"?`,
            showConfirmButton: true,
            showCancelButton: true
        });

        if (!result.isConfirmed)
            return;

        const data = await deleteTechnology(technology);

        if (data !== false) {
            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: data.success
            });
        }

        await load();
    }

    const deleteTechnology = async (technology: ITechnology): Promise<Record<'success', string> | false> => {
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
                return await deleteTechnology(technology);
            else
                return false;
        }
    }

    const deleteTechnologies = async () => {
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

        await Promise.all(toDelete.map(({ technology }) => deleteTechnology(technology)));

        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'Success!',
            text: `Deleted ${toDelete.length} technologies.`
        });

        await load();
    }

    const handleAddButtonClicked = async () => {
        const technology = await awaitModalPrompt(TechnologyPrompt);

        await addTechnology(technology);
    }

    const handleEditButtonClicked = async (technology: ITechnology) => {
        const updated = await awaitModalPrompt(TechnologyPrompt, { existing: technology });

        await editTechnology(updated);
    }

    const onItemSelected = (technology: ITechnology, selected: boolean) =>
        setTechnologies((technologies) => technologies.map((item) => item.technology === technology ? { technology, selected } : item));

    React.useEffect(() => {
        load();
    }, [renderCount]);

    const hasSelected = React.useMemo(() => {
        for (const { selected } of technologies) {
            if (selected)
                return true;
        }

        return false;
    }, [technologies]);

    return (
        <>
            <Row className="mb-3">
                <Col className="d-flex justify-content-between">
                    <div>
                        <Button color='primary' onClick={handleAddButtonClicked}>Add Technology</Button>
                    </div>

                    <div>
                        <Button color='primary' className="me-1" onClick={load}>
                            <span className='me-1'>
                                <FaSync />
                            </span>
                            Update
                        </Button>

                        <Button color="danger" disabled={!hasSelected} onClick={deleteTechnologies}>
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
                    <Technology
                        key={index}
                        technology={technology}
                        selected={selected}
                        onSelected={(selected) => onItemSelected(technology, selected)}
                        onEditClicked={() => handleEditButtonClicked(technology)}
                        onDeleteClicked={() => promptDeleteTechnology(technology)}
                    />
                ))}
                {technologies.length === 0 && <div>No technologies found.</div>}
            </Row>
        </>
    );
}

export default TechnologyList;
