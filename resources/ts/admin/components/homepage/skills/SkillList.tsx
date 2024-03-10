import React from 'react';
import { Button, Col, Row } from 'reactstrap';
import { FaEdit, FaSync, FaTimesCircle, FaTrash } from 'react-icons/fa';
import withReactContent from 'sweetalert2-react-content';

import Swal from 'sweetalert2';
import axios from 'axios';
import classNames from 'classnames';

import SkillPrompt from '@admin/components/homepage/skills/SkillPrompt';
import Icon from '@admin/components/icon-selector/Icon';

import { createAuthRequest } from '@admin/utils/api/factories';
import { defaultFormatter } from '@admin/utils/response-formatter/factories';
import { lookupIcon } from '@admin/components/icon-selector/utils';

interface IProps {

}

interface ISkillProps {
    skill: ISkill;
    selected: boolean;

    onEditClicked: () => void;
    onDeleteClicked: () => void;
    onSelected: (selected: boolean) => void;
}

interface ISkillItem {
    skill: ISkill;
    selected: boolean;
}

const Skill: React.FC<ISkillProps> = ({ skill, selected, onSelected, onEditClicked, onDeleteClicked }) => {
    const icon = React.useMemo(() => lookupIcon(skill.icon), [skill]);

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
                {skill.skill}
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

const SkillList: React.FC<IProps> = ({ }) => {
    const [addSkillPrompt, setAddSkillPrompt] = React.useState(false);
    const [editSkillPrompt, setEditSkillPrompt] = React.useState<ISkill | undefined>();
    const [skills, setSkills] = React.useState<ISkillItem[]>([]);

    const load = async () => {
        try {
            const response = await createAuthRequest().get<ISkill[]>('skills');

            setSkills(response.data.map((skill) => ({ skill, selected: false })));
        } catch (err) {
            console.error(err);

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
    }

    const addSkill = async (newSkill: ISkill) => {
        try {
            const response = await createAuthRequest().post('skills', newSkill);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Skill has been added.'
            });

            await load();
        } catch (err) {
            console.error(err);

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
    }

    const editSkill = async (skill: ISkill) => {
        try {
            const response = await createAuthRequest().put(`skills/${skill.id}`, skill);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Skill has been updated.'
            });

            await load();
        } catch (err) {
            console.error(err);

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
    }

    const promptDeleteSkill = async (skill: ISkill) => {
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
    }

    const deleteSkill = async (skill: ISkill): Promise<Record<'success', string> | false> => {
        try {
            const response = await createAuthRequest().delete<Record<'success', string>>(`skills/${skill.id}`);

            return response.data;
        } catch (err) {
            console.error(err);

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
    }

    const deleteSkills = async () => {
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
    }

    const onItemSelected = (skill: ISkill, selected: boolean) => {
        setSkills((skills) => skills.map((item) => item.skill === skill ? { skill, selected } : item));
    }

    const displayEditSkill = (skill: ISkill) => {
        setEditSkillPrompt(skill);
    }

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

            {addSkillPrompt && (
                <SkillPrompt
                    onSubmitted={addSkill}
                    onClose={() => setAddSkillPrompt(false)}
                />
            )}

            {editSkillPrompt && (
                <SkillPrompt
                    skill={editSkillPrompt}
                    onSubmitted={editSkill}
                    onClose={() => setEditSkillPrompt(undefined)}
                />
            )}

            <Row className="mb-3">
                <Col className="d-flex justify-content-between">
                    <div>
                        <Button color='primary' onClick={() => setAddSkillPrompt(true)}>Add Skill</Button>
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
                        onEditClicked={() => displayEditSkill(skill)}
                        onDeleteClicked={() => promptDeleteSkill(skill)}
                    />
                ))}
                {skills.length === 0 && <div>No skills found.</div>}
            </Row>
        </>
    );
}

export default SkillList;
