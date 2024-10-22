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

import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import awaitModalPrompt from '@admin/utils/modals';
import { addSkill, deleteSkill, loadSkills, updateSkill } from '@admin/utils/api/endpoints/skills';


interface IProps {

}

const SkillList: React.FC<IProps> = ({ }) => {
    const waitToLoadRef = React.useRef<IWaitToLoadHandle>(null);
    const [selected, setSelected] = React.useState<ISkill[]>([]);

    const load = React.useCallback(async () => loadSkills(), []);

    const reload = React.useCallback(() => {
        waitToLoadRef.current?.load();

        setSelected([]);
    }, [waitToLoadRef.current]);

    // TODO: Move to API services
    const handleAddSkill = React.useCallback(async (newSkill: ISkill) => {
        try {
            await addSkill(newSkill);

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
                await handleAddSkill(newSkill);
        }
    }, []);

    const handleEditSkill = React.useCallback(async (skill: ISkill) => {
        try {
            await updateSkill(skill);

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
                await handleEditSkill(skill);
        }
    }, []);

    const handleDeleteSkill = React.useCallback(async (skillId: number): Promise<Record<'success', string> | false> => {
        try {
            return await deleteSkill(skillId);
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
                return await handleDeleteSkill(skillId);
            else
                return false;
        }
    }, []);

    const promptDeleteSkill = React.useCallback(async (skill: ISkill) => {
        if (!skill.id) {
            logger.error(`Cannot delete skill without ID.`);
            return;
        }

        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Do you really want to remove "${skill.skill}"?`,
            showConfirmButton: true,
            showCancelButton: true
        });

        if (!result.isConfirmed)
            return;

        const data = await handleDeleteSkill(skill.id);

        if (data !== false) {
            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: data.success
            });
        }

        reload();
    }, [reload, handleDeleteSkill]);



    const handleItemSelected = React.useCallback((skill: ISkill, selected: boolean) => {
        setSelected((prev) => selected ? prev.concat(skill) : prev.filter((item) => item !== skill));
    }, []);

    const handleAddButtonClicked = React.useCallback(async () => {
        const skill = await awaitModalPrompt(SkillPrompt);

        await handleAddSkill(skill);

        reload();
    }, [reload, handleAddSkill]);

    const handleEditButtonClicked = React.useCallback(async (skill: ISkill) => {
        const updated = await awaitModalPrompt(SkillPrompt, { existing: skill });

        await handleEditSkill(updated);

        reload();
    }, [reload, handleEditSkill]);

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

        await Promise.all(selected.map((skill) => skill.id).map((skillId) => skillId && handleDeleteSkill(skillId)));

        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'Success!',
            text: `Deleted ${selected.length} skills.`
        });

        reload();
    }, [reload, selected, handleDeleteSkill]);

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
