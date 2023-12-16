import React from "react";
import { Col, Row } from "reactstrap";

import moment from "moment";

import VisitorsCard from "./stat-cards/VisitorsCard";
import BlogArticlesCard from "./stat-cards/BlogArticlesCard";
import CommentsCard from "./stat-cards/CommentsCard";
import ContactMessagesCard from "./stat-cards/ContactMessagesCard";

import { fetchArticles } from "@admin/utils/api/endpoints/articles";
import { loadAll } from "@admin/utils/api/endpoints/comments";
import { all } from "@admin/utils/api/endpoints/notifications";

import PaginateResponse from "@admin/utils/api/models/PaginateResponse";
import { isMessageNotification } from "@admin/store/slices/notifications";

interface IStatCardsProps {
    visitors: TApiState<IChartVisitors, unknown>;
}

const StatCards: React.FC<IStatCardsProps> = ({ visitors }) => {
    const [visitorsCount, setVisitorsCount] = React.useState<number>();
    const [articleCount, setArticleCount] = React.useState<number>();
    const [commentCount, setCommentCount] = React.useState<number>();

    const tryFetchArticles = async () => {
        try {
            const response = new PaginateResponse(await fetchArticles());

            setArticleCount(response.total);
        } catch (e) {
            if (import.meta.env.VITE_APP_DEBUG)
                logger.error(e);
        }
    }

    const tryFetchComments = async () => {
        try {
            const response = new PaginateResponse(await loadAll());

            setCommentCount(response.total);
        } catch (e) {
            if (import.meta.env.VITE_APP_DEBUG)
                logger.error(e);
        }
    }

    const tryFetchMessages = async () => {
        try {
            const notifications = await all();

            setCommentCount(notifications.filter(isMessageNotification).length);
        } catch (e) {
            if (import.meta.env.VITE_APP_DEBUG)
                logger.error(e);
        }
    }

    React.useEffect(() => {
        if (visitors.status !== 'fulfilled')
            return;

        const now = moment();

        // Sort visitor date ranges by closest to now
        // The key is start date/time in ISO8601 format
        const keyValues =
            Object.entries(visitors.response)
                .sort(([a], [b]) => moment(b).diff(now, 'seconds') - moment(a).diff(now, 'seconds'));

        // Gets closest date to now
        const closest = keyValues.shift();

        if (closest !== undefined)
            setVisitorsCount(closest[1].totalUsers);
    }, [visitors]);

    React.useEffect(() => {
        Promise.all([
            tryFetchArticles(),
            tryFetchComments(),
            tryFetchMessages(),
        ]);
    }, []);

    return (
        <Row>
            <Col md={6} xl={3} className="mb-4">
                <VisitorsCard count={visitorsCount} />
            </Col>

            <Col md={6} xl={3} className="mb-4">
                <BlogArticlesCard articles={articleCount} />
            </Col>

            <Col md={6} xl={3} className="mb-4">
                <CommentsCard comments={commentCount} />
            </Col>

            <Col md={6} xl={3} className="mb-4">
                <ContactMessagesCard messages={0} />
            </Col>
        </Row>
    );
}

export default StatCards;
