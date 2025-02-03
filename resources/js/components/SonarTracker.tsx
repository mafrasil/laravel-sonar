import React, { useEffect } from "react";

interface SonarTrackerProps {
    name: string;
    metadata?: Record<string, any>;
    trackAllHovers?: boolean;
    children: React.ReactElement;
}

export function SonarTracker({
    name,
    metadata,
    trackAllHovers,
    children,
}: SonarTrackerProps) {
    useEffect(() => {
        if (typeof trackAllHovers === "boolean") {
            window.__sonar.config.trackAllHovers = trackAllHovers;
        }
    }, [trackAllHovers]);

    return React.cloneElement(children, {
        "data-sonar": name,
        "data-sonar-metadata": metadata ? JSON.stringify(metadata) : undefined,
    });
}
