import store from "@admin/store/index";

declare global {
    export type RootState = ReturnType<typeof store.getState>;
    export type AppDispatch = typeof store.dispatch;
    export type LocationState = { };
}
