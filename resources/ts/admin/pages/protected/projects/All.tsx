import React from 'react';
import { Helmet } from 'react-helmet';
import { NavLink } from 'react-router-dom';
import { FaEdit, FaPlus, FaSync, FaTrash, FaUndo } from 'react-icons/fa';
import { Button, Card, CardBody, Col, Form, Input, Row, Table } from 'reactstrap';

import S from 'string';

import Heading from '@admin/layouts/admin/Heading';
import DeleteProjectModal from '@admin/components/projects/DeleteProjectModal';
import RestoreProjectModal from '@admin/components/projects/RestoreProjectModal';

import { createAuthRequest } from '@admin/utils/api/factories';

interface IProps {

}

interface IState {
    projects: IProject[];
    show: string;
}

interface IProjectProps {
    project: IProject;
    onDeleted: () => void;
    onRestored: () => void;
}

export default class All extends React.Component<IProps, IState> {
    static Project: React.FC<IProjectProps> = ({ project, onDeleted, onRestored }) => {
        const [showDeletePrompt, setShowDeletePrompt] = React.useState(false);
        const [showRestorePrompt, setShowRestorePrompt] = React.useState(false);

        const onDeleteClicked = () => {
            setShowDeletePrompt(true);
        }

        const onDeleteModalDeleted = () => {
            setShowDeletePrompt(false);
            onDeleted();
        }

        const onDeleteModalCanceled = () => {
            setShowDeletePrompt(false);
        }

        const onRestoreClicked = () => {
            setShowRestorePrompt(true);
        }

        const onRestoreModalDeleted = () => {
            setShowRestorePrompt(false);
            onRestored();
        }

        const onRestoreModalCanceled = () => {
            setShowRestorePrompt(false);
        }

        return (
            <>
                {showDeletePrompt && <DeleteProjectModal project={project} onDeleted={onDeleteModalDeleted} onCanceled={onDeleteModalCanceled} />}
                {showRestorePrompt && <RestoreProjectModal project={project} onRestored={onRestoreModalDeleted} onCanceled={onRestoreModalCanceled} />}
                <tr>
                    <td>{project.id}</td>
                    <td>{project.project}</td>
                    <td>{S(project.description).truncate(40).s}</td>
                    <td>
                        <a href={project.url} target='_blank'>
                            {project.url}
                        </a>
                    </td>
                    <td>
                        {
                            project.deleted_at === null ?
                            (
                                <>
                                    <Button tag={NavLink} to={`edit/${project.id}`} className='me-1'>
                                        <FaEdit />
                                    </Button>
                                    <Button color='danger' onClick={onDeleteClicked}>
                                        <FaTrash />
                                    </Button>
                                </>
                            ) :
                            (
                                <>
                                    <Button color='primary' onClick={onRestoreClicked}>
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

    constructor(props: Readonly<IProps>) {
        super(props);

        this.state = {
            projects: [],
            show: 'both'
        };

        this.fetchProjects = this.fetchProjects.bind(this);
        this.onUpdateFormSubmitted = this.onUpdateFormSubmitted.bind(this);
    }

    componentDidMount(): void {
        this.fetchProjects();
    }

    private async fetchProjects() {
        const { show } = this.state;

        try {
            const response = await createAuthRequest().get<IProject[]>('projects', { show });

            this.setState({ projects: response.data });
        } catch (e) {
            console.error(e);
        }
    }

    private async onUpdateFormSubmitted(e: React.FormEvent<HTMLFormElement>) {
        e.preventDefault();

        this.fetchProjects();
    }

    public render() {
        const { } = this.props;
        const { projects, show } = this.state;

        return (
            <>
                <Helmet>
                    <title>All Projects</title>
                </Helmet>

                <Heading>
                    <Heading.Title>All Projects</Heading.Title>
                </Heading>


                <Card>
                    <CardBody>
                        <Row>
                            <Col xs={12} className='d-flex justify-content-between mb-3'>
                                <div>
                                    <Button tag={NavLink} to='create' color='primary'>
                                        <FaPlus /> Create New
                                    </Button>
                                </div>
                                <div className="text-end">
                                    <Form className="row row-cols-lg-auto g-3" onSubmit={this.onUpdateFormSubmitted}>
                                        <Col xs={12}>
                                            <label className="visually-hidden" htmlFor="show">Show</label>

                                            <Input type='select' name='show' id='show' value={show} onChange={(e) => this.setState({ show: e.target.value })}>
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
                                        {projects.map((project, index) => <All.Project key={index} project={project} onRestored={this.fetchProjects} onDeleted={this.fetchProjects} />)}
                                    </tbody>
                                </Table>
                            </Col>
                        </Row>

                    </CardBody>
                </Card>
            </>
        );
    }
}
