import React from 'react';
import { Button, Col, Row } from 'reactstrap';
import { FaSync, FaTrash } from 'react-icons/fa';
import withReactContent from 'sweetalert2-react-content';

import Swal from 'sweetalert2';
import axios from 'axios';

import Technology from './Technology';
import TechnologyPrompt from './TechnologyPrompt';
import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import LoadError from '@admin/components/LoadError';
import Loader from '@admin/components/Loader';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import awaitModalPrompt from '@admin/utils/modals';


interface IProps {

}

const TechnologyList: React.FC<IProps> = ({ }) => {
    const waitToLoadRef = React.useRef<IWaitToLoadHandle>(null);
    const [selected, setSelected] = React.useState<ITechnology[]>([]);

    const load = React.useCallback(async () => {
        const response = await createAuthRequest().get<ITechnology[]>('technologies');

        return response.data;
    }, []);

    const reload = React.useCallback(() => {
        waitToLoadRef.current?.load();

        setSelected([]);
    }, [waitToLoadRef.current]);

    const addTechnology = React.useCallback(async (newTechnology: ITechnology) => {
        try {
            const response = await createAuthRequest().post('technologies', newTechnology);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Technology has been added.'
            });
        } catch (err) {
            logger.error(err);

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
        } catch (err) {
            logger.error(err);

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

    const deleteTechnology = React.useCallback(async (technology: ITechnology): Promise<Record<'success', string> | false> => {
        try {
            const response = await createAuthRequest().delete<Record<'success', string>>(`technologies/${technology.id}`);

            return response.data;
        } catch (err) {
            logger.error(err);

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

        reload();
    }, [reload, deleteTechnology]);

    const handleDeleteTechnologies = React.useCallback(async () => {
        if (selected.length === 0) {
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
            text: `Do you really want to remove ${selected.length} technologies?`,
            showConfirmButton: true,
            showCancelButton: true
        });

        if (!result.isConfirmed)
            return;

        await Promise.all(selected.map((technology) => deleteTechnology(technology)));

        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'Success!',
            text: `Deleted ${selected.length} technologies.`
        });

        reload();
    }, [reload, selected]);

    const handleAddButtonClicked = React.useCallback(async () => {
        const technology = await awaitModalPrompt(TechnologyPrompt);

        await addTechnology(technology);

        reload();
    }, [reload, addTechnology]);

    const handleEditButtonClicked = React.useCallback(async (technology: ITechnology) => {
        const updated = await awaitModalPrompt(TechnologyPrompt, { existing: technology });

        await editTechnology(updated);

        reload();
    }, [reload, editTechnology]);

    const handleItemSelected = React.useCallback((technology: ITechnology, selected: boolean) => {
        setSelected((prev) => selected ? prev.concat(technology) : prev.filter((item) => item !== technology));
    }, []);

    return (
        <>
            <Row className="mb-3">
                <Col className="d-flex flex-column flex-md-row justify-content-md-between">
                    <div className="mb-2 mb-md-0 d-flex flex-column flex-md-row">
                        <Button color='primary' onClick={handleAddButtonClicked}>Add Technology</Button>
                    </div>

                    <div className="d-flex flex-column flex-md-row">
                        <Button color='primary' className="me-md-1 mb-2 mb-md-0" onClick={load}>
                            <span className='me-1'>
                                <FaSync />
                            </span>
                            Update
                        </Button>

                        <Button color="danger" disabled={selected.length === 0} onClick={handleDeleteTechnologies}>
                            <span className='me-1'>
                                <FaTrash />
                            </span>
                            Delete Selected
                        </Button>
                    </div>
                </Col>
            </Row>

            <WaitToLoad
                ref={waitToLoadRef}
                callback={load}
                loading={<Loader display={{ type: 'over-element' }} />}
            >
                {(response, err, { reload }) => (
                    <>
                        {response && (
                            <Row className='mx-1 gap-2 justify-content-center'>
                                {response.length > 0 && response.map((technology, index) => (
                                    <Technology
                                        key={index}
                                        technology={technology}
                                        selected={selected.includes(technology)}
                                        onSelected={(selected) => handleItemSelected(technology, selected)}
                                        onEditClicked={() => handleEditButtonClicked(technology)}
                                        onDeleteClicked={() => promptDeleteTechnology(technology)}
                                    />
                                ))}
                                {response.length === 0 && <div>No technologies found.</div>}
                            </Row>
                        )}
                        {err && (
                            <LoadError
                                error={err}
                                onTryAgainClicked={() => reload()}
                                onGoBackClicked={() => window.history.back()}
                            />
                        )}
                    </>
                )}
            </WaitToLoad>

        </>
    );
}

export default TechnologyList;
