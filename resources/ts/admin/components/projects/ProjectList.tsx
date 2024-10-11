import React from 'react';
import { NavLink } from 'react-router-dom';
import { FaPlus, FaSync } from 'react-icons/fa';
import { Button, Col, Form, Input, Row, Table } from 'reactstrap';

import ProjectListRow from './ProjectListRow';
import WaitToLoad, { IWaitToLoadHandle, IWaitToLoadHelpers } from '@admin/components/WaitToLoad';
import LoadError from '@admin/components/LoadError';
import Loader from '@admin/components/Loader';

import { createAuthRequest } from '@admin/utils/api/factories';


interface IProps {

}

const ProjectList: React.FC<IProps> = ({ }) => {
    const waitToLoadRef = React.useRef<IWaitToLoadHandle>(null);
    const [show, setShow] = React.useState('both');

    const fetchProjects = React.useCallback(async () => {
        const response = await createAuthRequest().get<IProject[]>('projects', { show });

        return response.data;
    }, [show]);

    const handleUpdateFormSubmitted = React.useCallback(async (e: React.FormEvent<HTMLFormElement>) => {
        e.preventDefault();

        waitToLoadRef.current?.load();
    }, [waitToLoadRef.current]);

    return (
        <>
            <Row>
                <Col xs={12} className='d-flex flex-column flex-md-row justify-content-between mb-3'>
                    <div className="d-flex flex-column flex-md-row mb-3 mb-md-0">
                        <Button tag={NavLink} to='create' color='primary'>
                            <FaPlus /> Create New
                        </Button>
                    </div>
                    <div className="text-start text-md-end">
                        <Form className="row row-cols-lg-auto g-3" onSubmit={handleUpdateFormSubmitted}>
                            <Col xs={12}>
                                <label className="visually-hidden" htmlFor="show">Show</label>
                                <Input type='select' name='show' id='show' value={show} onChange={(e) => setShow(e.target.value)}>
                                    <option value="active">Active Only</option>
                                    <option value="inactive">Inactive Only</option>
                                    <option value="both">Both</option>
                                </Input>
                            </Col>
                            <Col xs={12} className='d-flex flex-column flex-md-row'>
                                <Button type='submit' color='primary'>
                                    <FaSync /> Update
                                </Button>
                            </Col>
                        </Form>
                    </div>
                </Col>

                <Col xs={12}>
                    <Table responsive>
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
                            <WaitToLoad
                                ref={waitToLoadRef}
                                callback={fetchProjects}
                                loading={<Loader display={{ type: 'over-element' }} />}
                            >
                                {(response, err, { reload }) => (
                                    <>
                                        {response && response.map((project, index) => (
                                            <ProjectListRow
                                                key={index}
                                                project={project}
                                                onRestored={() => reload()}
                                                onDeleted={() => reload()}
                                            />
                                        ))}
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
                        </tbody>
                    </Table>
                </Col>
            </Row>
        </>
    );
}

export default ProjectList;
