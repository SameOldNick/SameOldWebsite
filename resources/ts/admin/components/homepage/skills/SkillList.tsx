import React from 'react';
import { Button, Col, Row } from 'reactstrap';
import { FaSync, FaTrash } from 'react-icons/fa';
import withReactContent from 'sweetalert2-react-content';

import Swal from 'sweetalert2';
import axios from 'axios';

import Skill from '@admin/components/homepage/skills/Skill';
import SkillPrompt from '@admin/components/homepage/skills/SkillPrompt';
import WaitToLoad, { IWaitToLoadHandle } from '@admin/components/WaitToLoad';
import LoadError from '@admin/components/LoadError';
import Loader from '@admin/components/Loader';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import awaitModalPrompt from '@admin/utils/modals';


interface IProps {

}

const SkillList: React.FC<IProps> = ({ }) => {
    const waitToLoadRef = React.useRef<IWaitToLoadHandle>(null);
    const [selected, setSelected] = React.useState<ISkill[]>([]);

    const load = React.useCallback(async () => {
        const response = await createAuthRequest().get<ISkill[]>('skills');

        return response.data;
    }, []);

    const reload = React.useCallback(() => {
        waitToLoadRef.current?.load();

        setSelected([]);
    }, [waitToLoadRef.current]);

    // TODO: Move to API services
    const addSkill = React.useCallback(async (newSkill: ISkill) => {
        try {
            const response = await createAuthRequest().post('skills', newSkill);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Skill has been added.'
            });
        } catch (err) {
            logger.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `Unable to add skill: ${message}`,
                confirmButtonText: 'Try Again',
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed)
                await addSkill(newSkill);
        }
    }, []);

    const editSkill = React.useCallback(async (skill: ISkill) => {
        try {
            const response = await createAuthRequest().put(`skills/${skill.id}`, skill);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Skill has been updated.'
            });
        } catch (err) {
            logger.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `Unable to edit skill: ${message}`,
                confirmButtonText: 'Try Again',
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed)
                await editSkill(skill);
        }
    }, []);

    const deleteSkill = React.useCallback(async (skill: ISkill): Promise<Record<'success', string> | false> => {
        try {
            const response = await createAuthRequest().delete<Record<'success', string>>(`skills/${skill.id}`);

            return response.data;
        } catch (err) {
            logger.error(err);

            const message = defaultFormatter().parse(axios.isAxiosError(err) ? err.response : undefined);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `Unable to delete skill: ${message}`,
                confirmButtonText: 'Try Again',
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed)
                return await deleteSkill(skill);
            else
                return false;
        }
    }, []);

    const promptDeleteSkill = React.useCallback(async (skill: ISkill) => {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Do you really want to remove "${skill.skill}"?`,
            showConfirmButton: true,
            showCancelButton: true
        });

        if (!result.isConfirmed)
            return;

        const data = await deleteSkill(skill);

        if (data !== false) {
            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: data.success
            });
        }

        reload();
    }, [reload, deleteSkill]);



    const handleItemSelected = React.useCallback((skill: ISkill, selected: boolean) => {
        setSelected((prev) => selected ? prev.concat(skill) : prev.filter((item) => item !== skill));
    }, []);

    const handleAddButtonClicked = React.useCallback(async () => {
        const skill = await awaitModalPrompt(SkillPrompt);

        await addSkill(skill);

        reload();
    }, [reload, addSkill]);

    const handleEditButtonClicked = React.useCallback(async (skill: ISkill) => {
        const updated = await awaitModalPrompt(SkillPrompt, { existing: skill });

        await editSkill(updated);

        reload();
    }, [reload, editSkill]);

    const handleDeleteSkillsClicked = React.useCallback(async () => {
        if (selected.length === 0) {
            await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `No skills are selected.`
            });

            return;
        }

        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Do you really want to remove ${selected.length} skill(s)?`,
            showConfirmButton: true,
            showCancelButton: true
        });

        if (!result.isConfirmed)
            return;

        await Promise.all(selected.map((skill) => deleteSkill(skill)));

        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'Success!',
            text: `Deleted ${selected.length} skills.`
        });

        reload();
    }, [reload, selected, deleteSkill]);

    return (
        <>
            <Row className="mb-3">
                <Col className="d-flex flex-column flex-md-row justify-content-md-between">
                    <div className="mb-2 mb-md-0 d-flex flex-column flex-md-row">
                        <Button color='primary' onClick={handleAddButtonClicked}>Add Skill</Button>
                    </div>

                    <div className="d-flex flex-column flex-md-row">
                        <Button color='primary' className="me-md-1 mb-2 mb-md-0" onClick={() => reload()}>
                            <span className='me-1'>
                                <FaSync />
                            </span>
                            Update
                        </Button>

                        <Button color="danger" disabled={selected.length === 0} onClick={handleDeleteSkillsClicked}>
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
                            <>
                                <Row className='mx-1 gap-2 justify-content-center'>
                                    {response.length > 0 && response.map((skill, index) => (
                                        <Skill
                                            key={index}
                                            skill={skill}
                                            selected={selected.includes(skill)}
                                            onSelected={(selected) => handleItemSelected(skill, selected)}
                                            onEditClicked={() => handleEditButtonClicked(skill)}
                                            onDeleteClicked={() => promptDeleteSkill(skill)}
                                        />
                                    ))}
                                    {response.length === 0 && <div>No skills found.</div>}
                                </Row>
                            </>
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

export default SkillList;
