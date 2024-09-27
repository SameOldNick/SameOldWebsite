import * as React from 'react';
import { Button, Col, Row } from 'reactstrap';
import { FaDownload } from 'react-icons/fa';
import { Helmet } from 'react-helmet';

import Swal from 'sweetalert2';
import withReactContent from 'sweetalert2-react-content';

import Heading from '@admin/layouts/admin/Heading';

import DashboardCard from '@admin/components/dashboard/Card';
import WaitToLoad from '@admin/components/WaitToLoad';
import Loader from '@admin/components/Loader';

import VisitorsOverTime from './charts/VisitorsOverTime';
import PopularBrowsers from './charts/PopularBrowsers';
import PopularLinks from './charts/PopularLinks';

import StatCards from './StatCards';
import SecurityAlerts from './SecurityAlerts';
import RecentActivity from './recent-activity/RecentActivity';
import QuickLinks from './QuickLinks';

import { fetchPopularBrowsers, fetchPopularLinks, fetchVisitorsOverTime } from '@admin/utils/api/endpoints/dashboard';

interface IProps {

}

const Dashboard: React.FC<IProps> = ({ }) => {
    const [visitors, setVisitors] = React.useState<TApiState<IChartVisitors, unknown>>({ status: 'none' });

    const tryFetchVisitors = React.useCallback(async () => {
        try {
            const data = await fetchVisitorsOverTime();

            setVisitors({ status: 'fulfilled', response: data });
        } catch (err) {
            setVisitors({ status: 'rejected', error: err });
        }
    }, []);

    const handleGenerateReportClicked = React.useCallback(async () => {
        await withReactContent(Swal).fire({
            icon: 'info',
            title: 'Not Implemented',
            text: 'This feature is not yet implemented. Please check back later!',
        });
    }, []);

    const renderError = React.useCallback((err: unknown, status?: number) => {
        if (status === undefined && (err && typeof err === 'object' && 'status' in err)) {
            status = Number(err.status);
        }

        if (status === 501) {
            return (
                <p className="text-muted">(Not configured properly)</p>
            );
        } else {
            logger.error(err);

            return (
                <p className="text-muted">(An error occurred)</p>
            );
        }        
    }, []);

    React.useEffect(() => {
        tryFetchVisitors();
    }, []);

    return (
        <>
            <Helmet>
                <title>Dashboard</title>
            </Helmet>

            <Heading title="Dashboard">
                <Button size="sm" color="primary" className="d-none d-sm-inline-block shadow-sm" onClick={handleGenerateReportClicked}>
                    <FaDownload className='fa-sm text-white-50' /> Generate Report
                </Button>
            </Heading>

            {/* Content Row */}
            <StatCards visitors={visitors} />

            <Row>
                <Col md={8}>
                    <DashboardCard>
                        <DashboardCard.Header>
                            Visitors Over Time
                        </DashboardCard.Header>
                        <DashboardCard.Body>
                            {visitors.status === 'pending' && <Loader display={{ type: 'over-element' }} />}
                            {visitors.status === 'fulfilled' && <VisitorsOverTime data={visitors.response} />}
                            {visitors.status === 'rejected' && renderError(visitors.error)}
                        </DashboardCard.Body>
                    </DashboardCard>
                </Col>
                <Col md={4}>
                    <DashboardCard>
                        <DashboardCard.Header>
                            Popular Browsers
                        </DashboardCard.Header>
                        <DashboardCard.Body>
                            <WaitToLoad 
                                loading={<Loader display={{ type: 'over-element' }} />} 
                                callback={fetchPopularBrowsers}
                                log={false}
                            >
                                {(data, err) => (
                                    <>
                                        {data && <PopularBrowsers data={data} />}
                                        {err && renderError(err)}
                                    </>
                                )}
                            </WaitToLoad>
                        </DashboardCard.Body>
                    </DashboardCard>
                </Col>
            </Row>

            <Row>

                <Col xs={12}>
                    <Row className='row-cols-2'>
                        <Col>
                            <DashboardCard>
                                <DashboardCard.Header>
                                    Popular URLs
                                </DashboardCard.Header>
                                <DashboardCard.Body>
                                    <WaitToLoad 
                                        loading={<Loader display={{ type: 'over-element' }} />} 
                                        callback={fetchPopularLinks}
                                        log={false}
                                    >
                                        {(data, err) => (
                                            <>
                                                {data && <PopularLinks data={data} />}
                                                {err && renderError(err)}
                                            </>
                                        )}
                                    </WaitToLoad>
                                </DashboardCard.Body>
                            </DashboardCard>
                        </Col>

                        <Col>
                            <DashboardCard>
                                <DashboardCard.Header>
                                    Recent Activity
                                </DashboardCard.Header>
                                <DashboardCard.Body>
                                    <RecentActivity />
                                </DashboardCard.Body>
                            </DashboardCard>
                        </Col>

                        <Col>
                            <QuickLinks />
                        </Col>

                        <Col>
                            <DashboardCard>
                                <DashboardCard.Header>
                                    Security Alerts
                                </DashboardCard.Header>
                                <DashboardCard.Body>
                                    <SecurityAlerts />
                                </DashboardCard.Body>
                            </DashboardCard>
                        </Col>
                    </Row>
                </Col>
            </Row>
        </>
    );
}

export default Dashboard;
