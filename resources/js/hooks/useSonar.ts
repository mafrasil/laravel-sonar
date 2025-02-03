import { useCallback } from "react";
import { queueEvent, configureSonar } from "../core";
import type { SonarEventType } from "../types";

export function useSonar() {
    const track = useCallback(
        (
            name: string,
            type: SonarEventType,
            metadata?: Record<string, any>
        ) => {
            queueEvent(name, type, metadata);
        },
        []
    );

    const configure = useCallback(
        (config: Partial<typeof window.__sonar.config>) => {
            configureSonar(config);
        },
        []
    );

    return { track, configure };
}
