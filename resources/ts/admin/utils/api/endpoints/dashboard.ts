import { createAuthRequest } from "../factories";

export const fetchVisitorsOverTime = async () => {
    const response = await createAuthRequest().get<IChartVisitors>('/dashboard/visitors');

    return response.data;
}

export const fetchPopularBrowsers = async () => {
    const response = await createAuthRequest().get<IChartBrowsers>('/dashboard/browsers');

    return response.data;
}

export const fetchPopularLinks = async () => {
    const response = await createAuthRequest().get<IChartLinks>('/dashboard/links');

    return response.data;
}
