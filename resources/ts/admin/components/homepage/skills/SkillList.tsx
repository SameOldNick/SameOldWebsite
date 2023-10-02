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

interface IState {
    addSkill: boolean;
    editSkill?: ISkill;
    skills: ISkillItem[];
}

export default class SkillList extends React.Component<IProps, IState> {
    static Skill: React.FC<ISkillProps> = ({ skill, selected, onSelected, onEditClicked, onDeleteClicked }) => {
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

    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
            addSkill: false,
            skills: []
        };

        this.load = this.load.bind(this);
        this.addSkill = this.addSkill.bind(this);
        this.editSkill = this.editSkill.bind(this);
        this.deleteSkill = this.deleteSkill.bind(this);
        this.deleteSkills = this.deleteSkills.bind(this);
        this.promptDeleteSkill = this.promptDeleteSkill.bind(this);
        this.onItemSelected = this.onItemSelected.bind(this);
        this.displayEditSkill = this.displayEditSkill.bind(this);
    }

    componentDidMount() {
        this.load();
    }

    private async load() {
        try {
            const response = await createAuthRequest().get<ISkill[]>('skills');

            this.setState({ skills: response.data.map((skill) => ({ skill, selected: false })) });
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
                await this.load();
        }
    }

    private async addSkill(newSkill: ISkill) {
        try {
            const response = await createAuthRequest().post('skills', newSkill);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Skill has been added.'
            });

            await this.load();
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
                await this.addSkill(newSkill);
        }
    }

    private async editSkill(skill: ISkill) {
        try {
            const response = await createAuthRequest().put(`skills/${skill.id}`, skill);

            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: 'Skill has been updated.'
            });

            await this.load();
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
                await this.editSkill(skill);
        }
    }

    private async promptDeleteSkill(skill: ISkill) {
        const result = await withReactContent(Swal).fire({
            icon: 'question',
            title: 'Are You Sure?',
            text: `Do you really want to remove "${skill.skill}"?`,
            showConfirmButton: true,
            showCancelButton: true
        });

        if (!result.isConfirmed)
            return;

        const data = await this.deleteSkill(skill);

        if (data !== false) {
            await withReactContent(Swal).fire({
                icon: 'success',
                title: 'Success!',
                text: data.success
            });
        }

        await this.load();
    }

    private async deleteSkill(skill: ISkill): Promise<Record<'success', string> | false> {
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
                return await this.deleteSkill(skill);
            else
                return false;
        }
    }

    private async deleteSkills() {
        const { skills } = this.state;

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

        await Promise.all(toDelete.map(({ skill }) => this.deleteSkill(skill)));

        await withReactContent(Swal).fire({
            icon: 'success',
            title: 'Success!',
            text: `Deleted ${toDelete.length} skills.`
        });

        await this.load();
    }


    private onItemSelected(skill: ISkill, selected: boolean) {
        this.setState(({ skills }) => ({ skills: skills.map((item) => item.skill === skill ? { skill, selected } : item) }));
    }

    private displayEditSkill(skill: ISkill) {
        this.setState({ editSkill: skill });
    }

    public render() {
        const { } = this.props;
        const { addSkill, editSkill, skills } = this.state;

        const hasSelected = () => {
            for (const { selected } of skills) {
                if (selected)
                    return true;
            }

            return false;
        }

        return (
            <>

                {addSkill && (
                    <SkillPrompt
                        onSubmitted={this.addSkill}
                        onClose={() => this.setState({ addSkill: false })}
                    />
                )}

                {editSkill && (
                    <SkillPrompt
                        skill={editSkill}
                        onSubmitted={this.editSkill}
                        onClose={() => this.setState({ editSkill: undefined })}
                    />
                )}

                <Row className="mb-3">
                    <Col className="d-flex justify-content-between">
                        <div>
                            <Button color='primary' onClick={() => this.setState({ addSkill: true })}>Add Skill</Button>
                        </div>

                        <div>
                            <Button color='primary' className="me-1" onClick={this.load}>
                                <span className='me-1'>
                                    <FaSync />
                                </span>
                                Update
                            </Button>

                            <Button color="danger" disabled={!hasSelected()} onClick={this.deleteSkills}>
                                <span className='me-1'>
                                    <FaTrash />
                                </span>
                                Delete Selected
                            </Button>
                        </div>
                    </Col>
                </Row>

                <Row className='mx-1 gap-2'>
                    {skills.length > 0 && skills.map(({ skill, selected }, index) => (
                        <SkillList.Skill
                            key={index}
                            skill={skill}
                            selected={selected}
                            onSelected={(selected) => this.onItemSelected(skill, selected)}
                            onEditClicked={() => this.displayEditSkill(skill)}
                            onDeleteClicked={() => this.promptDeleteSkill(skill)}
                        />
                    ))}
                    {skills.length === 0 && <div>No skills found.</div>}
                </Row>
            </>
        );
    }
}
