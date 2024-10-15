import factory from "@admin/store/index";
import reducers from "@admin/store/reducers";

declare global {
    export type RootState = ReturnType<typeof reducers>;
    export type AppDispatch = ReturnType<typeof factory>['dispatch'];
}
