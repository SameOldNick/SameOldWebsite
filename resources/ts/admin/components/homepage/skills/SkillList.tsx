import React from 'react';
import { Button, Col, Row } from 'reactstrap';
import { FaSync, FaTrash } from 'react-icons/fa';
import withReactContent from 'sweetalert2-react-content';

import Swal from 'sweetalert2';
import axios from 'axios';

import Skill from '@admin/components/homepage/skills/Skill';
import SkillPrompt from '@admin/components/homepage/skills/SkillPrompt';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import awaitModalPrompt from '@admin/utils/modals';

interface IProps {

}

interface ISkillItem {
    skill: ISkill;
    selected: boolean;
}

const SkillList: React.FC<IProps> = ({ }) => {
    const [skills, setSkills] = React.useState<ISkillItem[]>([]);

    const load = React.useCallback(async () => {
        try {
            const response = await createAuthRequest().get<ISkill[]>('skills');

            setSkills(response.data.map((skill) => ({ skill, selected: false })));
        } catch (err) {
            logger.error(err);

            const result = await withReactContent(Swal).fire({
                icon: 'error',
                title: 'Oops...',
                text: `An error occurred trying to load skills.`,
                confirmButtonText: 'Try Again',
                showConfirmButton: true,
                showCancelButton: true
            });

            if (result.isConfirmed)
                await load();
        }
    }, []);

    const addSkill = React.useCallback(async (newSkill: ISkill) => {
        try {
            const response = await createAuthRequest().post('skills', newSkill);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Skill has been added.'
            });

            await load();
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
    }, [load]);

    const editSkill = React.useCallback(async (skill: ISkill) => {
        try {
            const response = await createAuthRequest().put(`skills/${skill.id}`, skill);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Skill has been updated.'
            });

            await load();
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
    }, [load]);

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

        await load();
    }, [load, deleteSkill]);

    const deleteSkills = React.useCallback(async () => {
        const toDelete = skills.filter((value) => value.selected);

        if (toDelete.length === 0) {
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
            text: `Do you really want to remove ${toDelete.length} skill(s)?`,
            showConfirmButton: true,
            showCancelButton: true
        });

        if (!result.isConfirmed)
            return;

        await Promise.all(toDelete.map(({ skill }) => deleteSkill(skill)));

        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'Success!',
            text: `Deleted ${toDelete.length} skills.`
        });

        await load();
    }, [load, skills, deleteSkill]);

    const onItemSelected = React.useCallback((skill: ISkill, selected: boolean) => {
        setSkills((skills) => skills.map((item) => item.skill === skill ? { skill, selected } : item));
    }, []);

    const handleAddButtonClicked = React.useCallback(async () => {
        const skill = await awaitModalPrompt(SkillPrompt);

        await addSkill(skill);
    }, [addSkill]);

    const handleEditButtonClicked = React.useCallback(async (skill: ISkill) => {
        const updated = await awaitModalPrompt(SkillPrompt, { existing: skill });

        await editSkill(updated);
    }, [editSkill]);

    React.useEffect(() => {
        load();
    }, []);

    const hasSelected = React.useMemo(() => {
        for (const { selected } of skills) {
            if (selected)
                return true;
        }

        return false;
    }, [skills]);

    return (
        <>
            <Row className="mb-3">
                <Col className="d-flex justify-content-between">
                    <div>
                        <Button color='primary' onClick={handleAddButtonClicked}>Add Skill</Button>
                    </div>

                    <div>
                        <Button color='primary' className="me-1" onClick={load}>
                            <span className='me-1'>
                                <FaSync />
                            </span>
                            Update
                        </Button>

                        <Button color="danger" disabled={!hasSelected} onClick={deleteSkills}>
                            <span className='me-1'>
                                <FaTrash />
                            </span>
                            Delete Selected
                        </Button>
                    </div>
                </Col>
            </Row>

            <Row className='mx-1 gap-2 justify-content-center'>
                {skills.length > 0 && skills.map(({ skill, selected }, index) => (
                    <Skill
                        key={index}
                        skill={skill}
                        selected={selected}
                        onSelected={(selected) => onItemSelected(skill, selected)}
                        onEditClicked={() => handleEditButtonClicked(skill)}
                        onDeleteClicked={() => promptDeleteSkill(skill)}
                    />
                ))}
                {skills.length === 0 && <div>No skills found.</div>}
            </Row>
        </>
    );
}

export default SkillList;
