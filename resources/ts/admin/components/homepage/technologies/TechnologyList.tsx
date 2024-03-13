import React from 'react';
import { Button, Col, Row } from 'reactstrap';
import { FaSync, FaTrash } from 'react-icons/fa';
import withReactContent from 'sweetalert2-react-content';

import Swal from 'sweetalert2';
import axios from 'axios';

import Technology from './Technology';
import TechnologyPrompt from './TechnologyPrompt';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import awaitModalPrompt from '@admin/utils/modals';

interface IProps {

}

interface ITechnologyItem {
    technology: ITechnology;
    selected: boolean;
}

const TechnologyList: React.FC<IProps> = ({ }) => {
    const [renderCount, setRenderCount] = React.useState(1);
    const [technologies, setTechnologies] = React.useState<ITechnologyItem[]>([]);

    const load = React.useCallback(async () => {
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
    }, []);

    const addTechnology = React.useCallback(async (newTechnology: ITechnology) => {
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
    }, []);

    const editTechnology = React.useCallback(async (technology: ITechnology) => {
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
    }, []);

    const promptDeleteTechnology = React.useCallback(async (technology: ITechnology) => {
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
    }, []);

    const deleteTechnology = React.useCallback(async (technology: ITechnology): Promise<Record<'success', string> | false> => {
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
    }, []);

    const deleteTechnologies = React.useCallback(async () => {
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
    }, []);

    const handleAddButtonClicked = React.useCallback(async () => {
        const technology = await awaitModalPrompt(TechnologyPrompt);

        await addTechnology(technology);
    }, []);

    const handleEditButtonClicked = React.useCallback(async (technology: ITechnology) => {
        const updated = await awaitModalPrompt(TechnologyPrompt, { existing: technology });

        await editTechnology(updated);
    }, []);

    const onItemSelected = React.useCallback(
        (technology: ITechnology, selected: boolean) =>
            setTechnologies((technologies) => technologies.map((item) => item.technology === technology ? { technology, selected } : item)),
        []);

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
