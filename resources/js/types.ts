export type SonarEventType = "click" | "hover" | "impression" | "custom";

export interface SonarEvent {
    name: string;
    type: SonarEventType;
    metadata?: Record<string, any>;
    timestamp: number;
    page: string;
    userAgent: string;
    screenSize: {
        width: number;
        height: number;
    };
}

export interface SonarState {
    queue: SonarEvent[];
    processing: boolean;
    impressed: Set<string>;
    hovered: Set<string>;
    observer: MutationObserver | null;
    queueTimeout: number | null;
    config: {
        trackAllHovers: boolean;
    };
}

declare global {
    interface Window {
        __sonar: SonarState;
    }
}
