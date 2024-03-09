import { createAuthRequest } from "../factories";

/**
 * Fetches visitors over time stats
 * @returns IChartVisitors object
 */
export const fetchVisitorsOverTime = async () => {
    const response = await createAuthRequest().get<IChartVisitors>('/dashboard/visitors');

    return response.data;
}

/**
 * Fetches popular browser stats
 * @returns IChartBrowsers object
 */
export const fetchPopularBrowsers = async () => {
    const response = await createAuthRequest().get<IChartBrowsers>('/dashboard/browsers');

    return response.data;
}

/**
 * Fetches popular link stats
 * @returns IChartLinks object
 */
export const fetchPopularLinks = async () => {
    const response = await createAuthRequest().get<IChartLinks>('/dashboard/links');

    return response.data;
}
