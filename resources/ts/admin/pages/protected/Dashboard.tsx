import React from 'react';
import { Button, Card, CardBody, CardHeader, Col, Dropdown, DropdownItem, DropdownMenu, DropdownToggle, Row } from 'reactstrap';
import { FaCalendar, FaCircle, FaClipboardList, FaComments, FaDollarSign, FaDownload, FaEllipsisV } from 'react-icons/fa';
import { Helmet } from 'react-helmet';

import Heading from '@admin/layouts/admin/Heading';

interface IProps {

}

const Dashboard: React.FC<IProps> = ({ }) => {
    const [dropdowns, setDropdowns] = React.useState({
        earnings: false,
        revenue: false
    });

    return (
        <>
            <Helmet>
                <title>Dashboard</title>
            </Helmet>

            <Heading title="Dashboard">
                <Button size="sm" color="primary" className="d-none d-sm-inline-block shadow-sm">
                    <FaDownload className='fa-sm text-white-50' /> Generate Report
                </Button>
            </Heading>

            {/* Content Row */}
            <Row>
                {/* Earnings (Monthly) Card Example */}
                <Col md={6} xl={3} className="mb-4">
                    <Card className='border-left-primary shadow h-100 py-2'>
                        <CardBody>
                            <Row className='no-gutters align-items-center'>
                                <Col className='me-2'>
                                    <div className="fw-bold text-primary text-uppercase mb-1">
                                        Earnings (Monthly)</div>
                                    <div className="h5 mb-0 fw-bold text-gray-800">$40,000</div>
                                </Col>
                                <Col xs='auto'>
                                    <FaCalendar className='fa-2x text-gray-300' />
                                </Col>
                            </Row>
                        </CardBody>
                    </Card>
                </Col>

                {/* Earnings (Monthly) Card Example */}
                <Col md={6} xl={3} className="mb-4">
                    <Card className='border-left-success shadow h-100 py-2'>
                        <CardBody>
                            <Row className='no-gutters align-items-center'>
                                <Col className='me-2'>
                                    <div className="fw-bold text-success text-uppercase mb-1">
                                        Earnings (Annual)
                                    </div>
                                    <div className="h5 mb-0 fw-bold text-gray-800">$215,000</div>
                                </Col>
                                <Col xs='auto'>
                                    <FaDollarSign className='fa-2x text-gray-300' />
                                </Col>
                            </Row>
                        </CardBody>
                    </Card>
                </Col>

                {/* Earnings (Monthly) Card Example */}
                <Col md={6} xl={3} className="mb-4">
                    <Card className="border-left-info shadow h-100 py-2">
                        <CardBody>
                            <Row className="no-gutters align-items-center">
                                <Col className="me-2">
                                    <div className="fw-bold text-info text-uppercase mb-1">
                                        Tasks
                                    </div>
                                    <Row className="no-gutters align-items-center">
                                        <Col xs='auto'>
                                            <div className="h5 mb-0 me-3 fw-bold text-gray-800">50%</div>
                                        </Col>
                                        <Col>
                                            <div className="progress progress-sm me-2">
                                                <div className="progress-bar bg-info" role="progressbar" style={{ width: '50%' }} aria-valuenow={50} aria-valuemin={0} aria-valuemax={100}></div>
                                            </div>
                                        </Col>
                                    </Row>
                                </Col>
                                <Col xs="auto">
                                    <FaClipboardList className="fa-2x text-gray-300" />
                                </Col>
                            </Row>
                        </CardBody>
                    </Card>
                </Col>

                {/* Pending Requests Card Example */}
                <Col md={6} xl={3} className="mb-4">
                    <Card className="border-left-warning shadow h-100 py-2">
                        <CardBody>
                            <Row className="no-gutters align-items-center">
                                <Col className="me-2">
                                    <div className="fw-bold text-warning text-uppercase mb-1">
                                        Pending Requests
                                    </div>
                                    <div className="h5 mb-0 fw-bold text-gray-800">18</div>
                                </Col>
                                <Col xs="auto">
                                    <FaComments className="fa-2x text-gray-300" />
                                </Col>
                            </Row>
                        </CardBody>
                    </Card>
                </Col>
            </Row>

            {/* Content Row */}

            <Row>

                {/* Area Chart */}
                <Col lg={7} xl={8}>
                    <Card>
                        <CardHeader className="py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 className="m-0 fw-bold text-primary">Earnings Overview</h6>

                            <Dropdown className='no-arrow' isOpen={dropdowns.earnings} toggle={() => setDropdowns((prevState) => ({ ...prevState, earnings: !prevState.earnings }))}>
                                <DropdownToggle caret tag='a' href='#'>
                                    <FaEllipsisV className="fa-sm fa-fw text-gray-400" />
                                </DropdownToggle>

                                <DropdownMenu end className='shadow animated--fade-in'>
                                    <DropdownItem header>Dropdown Header:</DropdownItem>
                                    <DropdownItem href='#'>Action</DropdownItem>
                                    <DropdownItem href='#'>Another Action</DropdownItem>
                                    <DropdownItem divider />
                                    <DropdownItem href='#'>Something else here</DropdownItem>
                                </DropdownMenu>
                            </Dropdown>
                        </CardHeader>

                        <CardBody>
                            <div className="chart-area">
                                <canvas id="myAreaChart"></canvas>
                            </div>
                        </CardBody>
                    </Card>
                </Col>

                {/* Pie Chart */}
                <Col lg={5} xl={4}>
                    <Card className="shadow mb-4">
                        {/* Card Header - Dropdown */}
                        <CardHeader className="py-3 d-flex flex-row align-items-center justify-content-between">
                            <h6 className="m-0 fw-bold text-primary">Revenue Sources</h6>
                            <Dropdown className='no-arrow' isOpen={dropdowns.revenue} toggle={() => setDropdowns((prevState) => ({ ...prevState, revenue: !prevState.revenue }))}>
                                <DropdownToggle caret tag='a' href='#'>
                                    <FaEllipsisV className="fa-sm fa-fw text-gray-400" />
                                </DropdownToggle>

                                <DropdownMenu end className='shadow animated--fade-in'>
                                    <DropdownItem header>Dropdown Header:</DropdownItem>
                                    <DropdownItem href='#'>Action</DropdownItem>
                                    <DropdownItem href='#'>Another Action</DropdownItem>
                                    <DropdownItem divider />
                                    <DropdownItem href='#'>Something else here</DropdownItem>
                                </DropdownMenu>
                            </Dropdown>
                        </CardHeader>
                        {/* Card Body */}
                        <CardBody>
                            <div className="chart-pie pt-4 pb-2">
                                <canvas id="myPieChart"></canvas>
                            </div>
                            <div className="mt-4 text-center small">
                                <span className="me-2">
                                    <FaCircle className="text-primary" /> Direct
                                </span>
                                <span className="me-2">
                                    <FaCircle className="text-success" /> Social
                                </span>
                                <span className="me-2">
                                    <FaCircle className="text-info" /> Referral
                                </span>
                            </div>
                        </CardBody>
                    </Card>
                </Col>
            </Row>

            {/* Content Row */}
            <Row>

                {/* Content Column */}
                <Col lg={6} className="mb-4">

                    {/* Project Card Example */}
                    <Card className="shadow mb-4">
                        <CardHeader className="py-3">
                            <h6 className="m-0 fw-bold text-primary">Projects</h6>
                        </CardHeader>
                        <CardBody>
                            <h4 className="small fw-bold">
                                Server Migration
                                <span className="float-end">20%</span>
                            </h4>
                            <div className="progress mb-4">
                                <div className="progress-bar bg-danger" role="progressbar" style={{ width: '20%' }} aria-valuenow={20} aria-valuemin={0} aria-valuemax={100}></div>
                            </div>
                            <h4 className="small fw-bold">
                                Sales Tracking
                                <span className="float-end">40%</span>
                            </h4>
                            <div className="progress mb-4">
                                <div className="progress-bar bg-warning" role="progressbar" style={{ width: '40%' }} aria-valuenow={40} aria-valuemin={0} aria-valuemax={100}></div>
                            </div>
                            <h4 className="small fw-bold">
                                Customer Database
                                <span className="float-end">60%</span>
                            </h4>
                            <div className="progress mb-4">
                                <div className="progress-bar" role="progressbar" style={{ width: '60%' }} aria-valuenow={60} aria-valuemin={0} aria-valuemax={100}></div>
                            </div>
                            <h4 className="small fw-bold">
                                Payout Details
                                <span className="float-end">80%</span>
                            </h4>
                            <div className="progress mb-4">
                                <div className="progress-bar bg-info" role="progressbar" style={{ width: '80%' }} aria-valuenow={80} aria-valuemin={0} aria-valuemax={100}></div>
                            </div>
                            <h4 className="small fw-bold">
                                Account Setup
                                <span className="float-end">Complete!</span>
                            </h4>
                            <div className="progress">
                                <div className="progress-bar bg-success" role="progressbar" style={{ width: '100%' }} aria-valuenow={100} aria-valuemin={0} aria-valuemax={100}></div>
                            </div>
                        </CardBody>
                    </Card>

                    {/* Color System */}
                    <Row>
                        <Col lg={6} className="mb-4">
                            <Card className="bg-primary text-white shadow">
                                <CardBody>
                                    Primary
                                    <div className="text-white-50 small">#4e73df</div>
                                </CardBody>
                            </Card>
                        </Col>
                        <Col lg={6} className="mb-4">
                            <Card className="bg-success text-white shadow">
                                <CardBody>
                                    Success
                                    <div className="text-white-50 small">#1cc88a</div>
                                </CardBody>
                            </Card>
                        </Col>
                        <Col lg={6} className="mb-4">
                            <Card className="bg-info text-white shadow">
                                <CardBody>
                                    Info
                                    <div className="text-white-50 small">#36b9cc</div>
                                </CardBody>
                            </Card>
                        </Col>
                        <Col lg={6} className="mb-4">
                            <Card className="bg-warning text-white shadow">
                                <CardBody>
                                    Warning
                                    <div className="text-white-50 small">#f6c23e</div>
                                </CardBody>
                            </Card>
                        </Col>
                        <Col lg={6} className="mb-4">
                            <Card className="bg-danger text-white shadow">
                                <CardBody>
                                    Danger
                                    <div className="text-white-50 small">#e74a3b</div>
                                </CardBody>
                            </Card>
                        </Col>
                        <Col lg={6} className="mb-4">
                            <Card className="bg-secondary text-white shadow">
                                <CardBody>
                                    Secondary
                                    <div className="text-white-50 small">#858796</div>
                                </CardBody>
                            </Card>
                        </Col>
                        <Col lg={6} className="mb-4">
                            <Card className="bg-light text-black shadow">
                                <CardBody>
                                    Light
                                    <div className="text-black-50 small">#f8f9fc</div>
                                </CardBody>
                            </Card>
                        </Col>
                        <Col lg={6} className="mb-4">
                            <Card className="bg-dark text-white shadow">
                                <CardBody>
                                    Dark
                                    <div className="text-white-50 small">#5a5c69</div>
                                </CardBody>
                            </Card>
                        </Col>
                    </Row>

                </Col>

                <Col lg={6} className="mb-4">
                    {/* Illustrations */}
                    <Card className="shadow mb-4">
                        <CardHeader className="py-3">
                            <h6 className="m-0 fw-bold text-primary">Illustrations</h6>
                        </CardHeader>
                        <CardBody>
                            <div className="text-center">
                                <img className="img-fluid px-3 px-sm-4 mt-3 mb-4" style={{ width: '25rem' }} src="img/undraw_posting_photo.svg" alt="..." />
                            </div>
                            <p>
                                Add some quality, svg illustrations to your project courtesy of
                                <a target="_blank" rel="noreferrer nofollow" href="https://undraw.co/">unDraw</a>
                                , a constantly updated collection of beautiful svg images that you can use
                                completely free and without attribution!
                            </p>
                            <a target="_blank" rel="noreferrer nofollow" href="https://undraw.co/">Browse Illustrations on unDraw &rarr; </a>
                        </CardBody>
                    </Card>

                    {/* Approach */}
                    <Card className="shadow mb-4">
                        <CardHeader className="py-3">
                            <h6 className="m-0 fw-bold text-primary">Development Approach</h6>
                        </CardHeader>
                        <CardBody>
                            <p>
                                SB Admin 2 makes extensive use of Bootstrap 4 utility classes in order to reduce
                                CSS bloat and poor page performance. Custom CSS classes are used to create
                                custom components and custom utility classes.
                            </p>
                            <p className="mb-0">
                                Before working with this theme, you should become familiar with the
                                Bootstrap framework, especially the utility classes.
                            </p>
                        </CardBody>
                    </Card>

                </Col>
            </Row>
        </>
    );
}

export default Dashboard;
