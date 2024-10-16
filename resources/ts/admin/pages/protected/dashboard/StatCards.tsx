import React from "react";
import { Col, Row } from "reactstrap";

import VisitorsCard from "./stat-cards/VisitorsCard";
import BlogArticlesCard from "./stat-cards/BlogArticlesCard";
import CommentsCard from "./stat-cards/CommentsCard";

import { fetchArticles } from "@admin/utils/api/endpoints/articles";
import { loadAll } from "@admin/utils/api/endpoints/comments";
import PaginateResponse from "@admin/utils/api/models/PaginateResponse";

import { DateTime } from "luxon";

interface IStatCardsProps {
    visitors: TApiState<IChartVisitors, unknown>;
}

const StatCards: React.FC<IStatCardsProps> = ({ visitors }) => {
    const [visitorsCount, setVisitorsCount] = React.useState<number>();
    const [articleCount, setArticleCount] = React.useState<number>();
    const [commentCount, setCommentCount] = React.useState<number>();

    const tryFetchArticles = React.useCallback(async () => {
        try {
            const response = new PaginateResponse(await fetchArticles());

            setArticleCount(response.total);
        } catch (e) {
            if (import.meta.env.VITE_APP_DEBUG)
                logger.error(e);
        }
    }, []);

    const tryFetchComments = React.useCallback(async () => {
        try {
            const response = new PaginateResponse(await loadAll());

            setCommentCount(response.total);
        } catch (e) {
            if (import.meta.env.VITE_APP_DEBUG)
                logger.error(e);
        }
    }, []);

    React.useEffect(() => {
        if (visitors.status !== 'fulfilled')
            return;

        // Sort visitor date ranges by closest to now
        // The key is start date/time in ISO8601 format
        const keyValues =
            Object.entries(visitors.response)
                .sort(([a], [b]) => DateTime.fromISO(b).diffNow().seconds - DateTime.fromISO(a).diffNow().seconds);

        // Gets closest date to now
        const closest = keyValues.shift();

        if (closest !== undefined)
            setVisitorsCount(closest[1].totalUsers);
    }, [visitors]);

    React.useEffect(() => {
        Promise.all([
            tryFetchArticles(),
            tryFetchComments(),
        ]);
    }, []);

    // TODO: Add count-up animation

    return (
        <Row>
            <Col md={12} xl={4} className="mb-4">
                <VisitorsCard count={visitorsCount} />
            </Col>

            <Col md={12} xl={4} className="mb-4">
                <BlogArticlesCard articles={articleCount} />
            </Col>

            <Col md={12} xl={4} className="mb-4">
                <CommentsCard comments={commentCount} />
            </Col>
        </Row>
    );
}

export default StatCards;
