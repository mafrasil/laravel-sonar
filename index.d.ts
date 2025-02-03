declare module "@laravel-sonar" {
    export { SonarTracker } from "./dist/components/SonarTracker";
    export { useSonar } from "./dist/hooks/useSonar";
    export type { SonarEventType, SonarEvent } from "./dist/types";
}
