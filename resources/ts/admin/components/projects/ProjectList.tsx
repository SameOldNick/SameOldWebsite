import React from 'react';
import { NavLink } from 'react-router-dom';
import { FaEdit, FaPlus, FaSync, FaTrash, FaUndo } from 'react-icons/fa';
import { Button, Col, Form, Input, Row, Table } from 'reactstrap';

import S from 'string';

import DeleteProjectModal from '@admin/components/projects/DeleteProjectModal';
import RestoreProjectModal from '@admin/components/projects/RestoreProjectModal';

import { createAuthRequest } from '@admin/utils/api/factories';
import awaitModalPrompt from '@admin/utils/modals';

interface IProps {

}

interface IProjectProps {
    project: IProject;
    onDeleted: () => void;
    onRestored: () => void;
}

const Project: React.FC<IProjectProps> = ({ project, onDeleted, onRestored }) => {
    const handleDeleteClicked = React.useCallback(async () => {
        try {
            await awaitModalPrompt(DeleteProjectModal, { project });

            onDeleted();
        } catch (err) {
            // Modal was cancelled.
        }
    }, [onDeleted]);

    const handleRestoreClicked = React.useCallback(async () => {
        try {
            await awaitModalPrompt(RestoreProjectModal, { project });

            onRestored();
        } catch (err) {
            // Modal was cancelled.
        }
    }, [onRestored]);

    return (
        <>
            <tr>
                <td>{project.id}</td>
                <td>{project.project}</td>
                <td>{S(project.description).truncate(40).s}</td>
                <td>
                    <a href={project.url} target='_blank' rel='noreferrer'>
                        {project.url}
                    </a>
                </td>
                <td>
                    {
                        project.deleted_at === null ?
                            (
                                <>
                                    <Button color='primary' tag={NavLink} to={`edit/${project.id}`} className='me-1'>
                                        <FaEdit />
                                    </Button>
                                    <Button color='danger' onClick={handleDeleteClicked}>
                                        <FaTrash />
                                    </Button>
                                </>
                            ) :
                            (
                                <>
                                    <Button color='primary' onClick={handleRestoreClicked}>
                                        <FaUndo />
                                    </Button>
                                </>
                            )
                    }

                </td>
            </tr>
        </>
    );
}

const ProjectList: React.FC<IProps> = ({ }) => {
    const [projects, setProjects] = React.useState<IProject[]>([]);
    const [show, setShow] = React.useState('both');

    const fetchProjects = React.useCallback(async () => {

        try {
            const response = await createAuthRequest().get<IProject[]>('projects', { show });

            setProjects(response.data);
        } catch (e) {
            console.error(e);
        }
    }, [show]);

    const onUpdateFormSubmitted = React.useCallback(async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        fetchProjects();
    }, []);

    React.useEffect(() => {
        fetchProjects();
    }, []);

    return (
        <>
            <Row>
                <Col xs={12} className='d-flex justify-content-between mb-3'>
                    <div>
                        <Button tag={NavLink} to='create' color='primary'>
                            <FaPlus /> Create New
                        </Button>
                    </div>
                    <div className="text-end">
                        <Form className="row row-cols-lg-auto g-3" onSubmit={onUpdateFormSubmitted}>
                            <Col xs={12}>
                                <label className="visually-hidden" htmlFor="show">Show</label>

                                <Input type='select' name='show' id='show' value={show} onChange={(e) => setShow(e.target.value)}>
                                    <option value="active">Active Only</option>
                                    <option value="inactive">Inactive Only</option>
                                    <option value="both">Both</option>
                                </Input>
                            </Col>
                            <Col xs={12}>
                                <Button type='submit' color='primary'>
                                    <FaSync /> Update
                                </Button>
                            </Col>
                        </Form>

                    </div>
                </Col>
                <Col xs={12}>
                    <Table>
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Title</th>
                                <th>Description</th>
                                <th>URL</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            {projects.map((project, index) =>
                                <Project
                                    key={index}
                                    project={project}
                                    onRestored={fetchProjects}
                                    onDeleted={fetchProjects}
                                />
                            )}
                        </tbody>
                    </Table>
                </Col>
            </Row>
        </>
    );
}

export default ProjectList;
